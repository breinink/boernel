/* play.js — Gronings Klaverjassen single-player vs AI
 * Wrapped in IIFE to avoid polluting globals shared with app.js
 */
(function () {
'use strict';

// ============================================================
// Constants
// ============================================================
var SUITS = ['H', 'S', 'D', 'C'];
var RANKS = ['7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

// Card point values when this suit IS trump
var TRUMP_VALUES  = { 'J': 20, '9': 14, 'A': 11, '10': 10, 'K': 4, 'Q': 3, '8': 0, '7': 0 };
// Card point values when this suit is NOT trump
var PLAIN_VALUES  = { 'A': 11, '10': 10, 'K': 4, 'Q': 3, 'J': 2, '9': 0, '8': 0, '7': 0 };

// Rank order for comparison — index 0 is highest card
var TRUMP_RANK_ORDER = ['J', '9', 'A', '10', 'K', 'Q', '8', '7'];
var PLAIN_RANK_ORDER = ['A', '10', 'K', 'Q', 'J', '9', '8', '7'];

// Sequence detection order (ascending: 7 lowest, A highest)
var SEQ_ORDER = ['7', '8', '9', '10', 'J', 'Q', 'K', 'A'];

var BID_STEPS = [70, 80, 90, 100, 110, 120, 130, 140, 150, 162];

// Play order (clockwise from South): Wij=Zuid+Noord, Zij=West+Oost
// Adjacent players are opponents; player 2 positions away is partner.
var SEAT_ORDER = ['Zuid', 'West', 'Noord', 'Oost'];
var WIJ_TEAM   = ['Zuid', 'Noord'];
var ZIJ_TEAM   = ['West', 'Oost'];

// Visual / naming helpers
var SUIT_SYMBOLS = { 'H': '♥', 'S': '♠', 'D': '♦', 'C': '♣' };
var SUIT_NAMES   = { 'H': 'Harten', 'S': 'Schoppen', 'D': 'Ruiten', 'C': 'Klaveren' };
var CARD_IMG_PATH = './bestanden/cards/';

// AI timing delays (ms) — tune these constants to change pacing
var DELAY_AI_BID    = 700;
var DELAY_AI_PLAY   = 900;
var DELAY_TRICK_END = 1400;
var DELAY_ROEM_STEP = 450;

// Game phases
var PHASE = {
    IDLE:     'idle',
    DEALING:  'dealing',
    BIDDING:  'bidding',
    ROEM:     'roem',
    PLAYING:  'playing',
    SCORING:  'scoring',
    GAMEOVER: 'gameover'
};

// ============================================================
// Card
// ============================================================
function Card(rank, suit) {
    this.rank = rank;
    this.suit = suit;
}

Object.defineProperties(Card.prototype, {
    id:       { get: function () { return this.rank + this.suit; } },
    filename: { get: function () { return this.rank + this.suit + '.svg'; } },
    imgSrc:   { get: function () { return CARD_IMG_PATH + this.filename; } },
    label:    { get: function () { return this.rank + SUIT_SYMBOLS[this.suit]; } }
});

Card.prototype.value = function (trump) {
    return this.suit === trump ? TRUMP_VALUES[this.rank] : PLAIN_VALUES[this.rank];
};

Card.prototype.rankIndex = function (trump) {
    var order = this.suit === trump ? TRUMP_RANK_ORDER : PLAIN_RANK_ORDER;
    return order.indexOf(this.rank);
};

/**
 * Returns true if this card beats `other` given trump suit and the led suit.
 * Trump beats non-trump; within same category, compare rank order.
 */
Card.prototype.beats = function (other, trump, ledSuit) {
    var thisTrump  = this.suit === trump;
    var otherTrump = other.suit === trump;

    if (thisTrump && !otherTrump) return true;
    if (!thisTrump && otherTrump) return false;

    if (thisTrump) {
        // Both trump — compare trump rank (lower index = higher card)
        return TRUMP_RANK_ORDER.indexOf(this.rank) < TRUMP_RANK_ORDER.indexOf(other.rank);
    }

    // Neither is trump — only the led suit can win
    var thisLed  = this.suit === ledSuit;
    var otherLed = other.suit === ledSuit;

    if (thisLed && !otherLed) return true;
    if (!thisLed && otherLed) return false;

    if (thisLed) {
        // Both led suit — compare plain rank
        return PLAIN_RANK_ORDER.indexOf(this.rank) < PLAIN_RANK_ORDER.indexOf(other.rank);
    }

    return false; // neither trump nor led suit — cannot win
};

// ============================================================
// Deck
// ============================================================
function Deck() {
    this._cards = [];
    for (var si = 0; si < SUITS.length; si++) {
        for (var ri = 0; ri < RANKS.length; ri++) {
            this._cards.push(new Card(RANKS[ri], SUITS[si]));
        }
    }
}

Deck.prototype.shuffle = function () {
    var a = this._cards;
    for (var i = a.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var tmp = a[i]; a[i] = a[j]; a[j] = tmp;
    }
    return this;
};

// Returns array of numPlayers arrays each containing cardsEach Card objects
Deck.prototype.deal = function (numPlayers, cardsEach) {
    var hands = [];
    for (var i = 0; i < numPlayers; i++) hands.push([]);
    for (var c = 0; c < cardsEach * numPlayers; c++) {
        hands[c % numPlayers].push(this._cards[c]);
    }
    return hands;
};

// ============================================================
// Hand
// ============================================================
function Hand(cards, playerId) {
    this._cards   = cards.slice();
    this.playerId = playerId;
}

Object.defineProperties(Hand.prototype, {
    cards: { get: function () { return this._cards.slice(); } },
    size:  { get: function () { return this._cards.length; } }
});

Hand.prototype.remove = function (card) {
    var idx = -1;
    for (var i = 0; i < this._cards.length; i++) {
        if (this._cards[i].id === card.id) { idx = i; break; }
    }
    if (idx !== -1) this._cards.splice(idx, 1);
};

Hand.prototype.hasSuit = function (suit) {
    return this._cards.some(function (c) { return c.suit === suit; });
};

Hand.prototype.cardsOfSuit = function (suit) {
    return this._cards.filter(function (c) { return c.suit === suit; });
};

/**
 * Returns the subset of cards that are legal to play.
 * ledSuit is null when this player is leading the trick.
 */
Hand.prototype.validPlays = function (ledSuit, trump) {
    if (!ledSuit) return this._cards.slice(); // leading: any card

    var suitCards = this.cardsOfSuit(ledSuit);
    if (suitCards.length > 0) return suitCards; // must follow suit

    return this._cards.slice(); // Amsterdamse regels: geen verplicht troeven
};

/**
 * Detect all roem (bonus points) in this hand given the trump suit.
 * Returns array of { type, cards, points, description }.
 */
Hand.prototype.detectRoem = function (trump) {
    var results = [];

    // Stuk: K + Q of trump in same hand = 20 pts
    if (trump) {
        var hasK = this._cards.some(function (c) { return c.rank === 'K' && c.suit === trump; });
        var hasQ = this._cards.some(function (c) { return c.rank === 'Q' && c.suit === trump; });
        if (hasK && hasQ) {
            var stukCards = this._cards.filter(function (c) {
                return (c.rank === 'K' || c.rank === 'Q') && c.suit === trump;
            });
            results.push({ type: 'stuk', cards: stukCards, points: 20,
                description: 'Stuk ' + SUIT_SYMBOLS[trump] });
        }
    }

    // Sequences: consecutive runs of 3+ cards in the same suit
    for (var si = 0; si < SUITS.length; si++) {
        var suit = SUITS[si];
        var suitIndices = this._cards
            .filter(function (c) { return c.suit === suit; })
            .map(function (c) { return SEQ_ORDER.indexOf(c.rank); })
            .sort(function (a, b) { return a - b; });

        if (suitIndices.length < 3) continue;

        var runStart = 0;
        for (var i = 1; i <= suitIndices.length; i++) {
            var isEnd = i === suitIndices.length || suitIndices[i] !== suitIndices[i - 1] + 1;
            if (isEnd) {
                var runLen = i - runStart;
                if (runLen >= 3) {
                    var runIdxSlice = suitIndices.slice(runStart, i);
                    var runCards = [];
                    for (var ri = 0; ri < runIdxSlice.length; ri++) {
                        var seqIdx = runIdxSlice[ri];
                        for (var ci = 0; ci < this._cards.length; ci++) {
                            if (this._cards[ci].suit === suit &&
                                SEQ_ORDER.indexOf(this._cards[ci].rank) === seqIdx) {
                                runCards.push(this._cards[ci]);
                                break;
                            }
                        }
                    }
                    var pts = runLen >= 5 ? 100 : (runLen === 4 ? 50 : 20);
                    results.push({
                        type: 'sequence', cards: runCards, points: pts,
                        description: runLen + '-reeks ' + SUIT_SYMBOLS[suit]
                    });
                }
                runStart = i;
            }
        }
    }

    return results;
};

// ============================================================
// Trick
// ============================================================
function Trick(leaderId, trump) {
    this.leaderId = leaderId;
    this.trump    = trump;
    this._plays   = []; // [{playerId, card}]
}

Object.defineProperties(Trick.prototype, {
    ledSuit:  { get: function () { return this._plays.length > 0 ? this._plays[0].card.suit : null; } },
    complete: { get: function () { return this._plays.length === 4; } },
    plays:    { get: function () { return this._plays.slice(); } }
});

Trick.prototype.addPlay = function (playerId, card) {
    this._plays.push({ playerId: playerId, card: card });
};

Object.defineProperty(Trick.prototype, 'winner', {
    get: function () {
        if (!this.complete) return null;
        var best = this._plays[0];
        for (var i = 1; i < this._plays.length; i++) {
            if (this._plays[i].card.beats(best.card, this.trump, this.ledSuit)) {
                best = this._plays[i];
            }
        }
        return best.playerId;
    }
});

Object.defineProperty(Trick.prototype, 'points', {
    get: function () {
        var trump = this.trump;
        return this._plays.reduce(function (sum, p) { return sum + p.card.value(trump); }, 0);
    }
});

// ============================================================
// Roem detectie op tafel (Amsterdamse regels)
// Controleert de 4 gespeelde kaarten op roem; winnaar van de slag krijgt de punten.
// ============================================================
function detectTrickRoem(plays, trump) {
    var cards = plays.map(function (p) { return p.card; });
    var results = [];

    // Stuk: K + Q van troef in dezelfde slag → 20 pt
    if (trump) {
        var hasK = cards.some(function (c) { return c.rank === 'K' && c.suit === trump; });
        var hasQ = cards.some(function (c) { return c.rank === 'Q' && c.suit === trump; });
        if (hasK && hasQ) {
            results.push({ type: 'stuk', points: 20, description: 'Stuk ' + SUIT_SYMBOLS[trump] });
        }
    }

    // Reeksen: 3+ opeenvolgende kaarten van hetzelfde symbool in de slag
    for (var si = 0; si < SUITS.length; si++) {
        var suit = SUITS[si];
        var idxs = cards
            .filter(function (c) { return c.suit === suit; })
            .map(function (c) { return SEQ_ORDER.indexOf(c.rank); })
            .sort(function (a, b) { return a - b; });
        if (idxs.length < 3) continue;
        var runStart = 0;
        for (var i = 1; i <= idxs.length; i++) {
            if (i === idxs.length || idxs[i] !== idxs[i - 1] + 1) {
                var runLen = i - runStart;
                if (runLen >= 3) {
                    var pts = runLen >= 5 ? 100 : (runLen === 4 ? 50 : 20);
                    results.push({ type: 'sequence', points: pts,
                        description: runLen + '-reeks ' + SUIT_SYMBOLS[suit] });
                }
                runStart = i;
            }
        }
    }
    return results;
}

// ============================================================
// AI Strategy — extensible pattern
// ============================================================
function AIStrategy() {}
AIStrategy.prototype.bid = function (hand, currentHighBid, mustBid, position) { return null; };
AIStrategy.prototype.chooseCard = function (hand, trick, trump, gameState) { return hand.cards[0]; };

// ---- Level 1: simple heuristic ----
function AIStrategyLevel1() {}
AIStrategyLevel1.prototype = Object.create(AIStrategy.prototype);

AIStrategyLevel1.prototype.bid = function (hand, currentHighBid, mustBid) {
    var minBid = currentHighBid ? currentHighBid.amount + 10 : 70;

    // Score each suit by presence of J and 9
    var bestSuit = null, bestScore = -1;
    for (var si = 0; si < SUITS.length; si++) {
        var suit = SUITS[si];
        var cards = hand.cards;
        var hasJ = cards.some(function (c) { return c.rank === 'J' && c.suit === suit; });
        var has9 = cards.some(function (c) { return c.rank === '9' && c.suit === suit; });
        var score = (hasJ ? 2 : 0) + (has9 ? 1 : 0);
        if (score > bestScore) { bestScore = score; bestSuit = suit; }
    }

    var bidAmount;
    if (bestScore >= 3) {        // J + 9 of same suit
        bidAmount = 80;
    } else if (bestScore >= 2) { // J only
        bidAmount = 70;
    } else if (mustBid) {
        // Forced: choose suit with highest total trump point value
        var maxPts = -1;
        for (var si2 = 0; si2 < SUITS.length; si2++) {
            var s = SUITS[si2];
            var pts = hand.cards
                .filter(function (c) { return c.suit === s; })
                .reduce(function (sum, c) { return sum + TRUMP_VALUES[c.rank]; }, 0);
            if (pts > maxPts) { maxPts = pts; bestSuit = s; }
        }
        bidAmount = 70;
    } else {
        return null; // pass
    }

    if (bidAmount < minBid) {
        if (mustBid) return { suit: bestSuit, amount: minBid };
        return null;
    }

    return { suit: bestSuit, amount: bidAmount };
};

AIStrategyLevel1.prototype.chooseCard = function (hand, trick, trump) {
    var valid = hand.validPlays(trick.ledSuit, trump);
    if (valid.length === 0) return hand.cards[0];

    if (trick.plays.length === 0) {
        // Leading: play highest card by rank
        return this._highestCard(valid, trump);
    }

    var ledSuit = trick.ledSuit;
    var canFollow = valid.some(function (c) { return c.suit === ledSuit; });

    if (canFollow) {
        return this._highestOfSuit(valid, ledSuit, trump);
    }

    var canTrump = valid.some(function (c) { return c.suit === trump; });
    if (canTrump) {
        return this._lowestOfSuit(valid, trump, trump);
    }

    return this._lowestCard(valid, trump);
};

AIStrategyLevel1.prototype._highestCard = function (cards, trump) {
    return cards.reduce(function (best, c) {
        return c.rankIndex(trump) < best.rankIndex(trump) ? c : best;
    });
};

AIStrategyLevel1.prototype._highestOfSuit = function (cards, suit, trump) {
    var sc = cards.filter(function (c) { return c.suit === suit; });
    if (!sc.length) return cards[0];
    return sc.reduce(function (best, c) {
        return c.rankIndex(trump) < best.rankIndex(trump) ? c : best;
    });
};

AIStrategyLevel1.prototype._lowestOfSuit = function (cards, suit, trump) {
    var sc = cards.filter(function (c) { return c.suit === suit; });
    if (!sc.length) return cards[0];
    return sc.reduce(function (best, c) {
        return c.rankIndex(trump) > best.rankIndex(trump) ? c : best;
    });
};

AIStrategyLevel1.prototype._lowestCard = function (cards, trump) {
    return cards.reduce(function (best, c) {
        return c.rankIndex(trump) > best.rankIndex(trump) ? c : best;
    });
};

// ============================================================
// AI
// ============================================================
function AI(playerId, strategyLevel) {
    this.playerId = playerId;
    this.strategy = AI.createStrategy(strategyLevel);
}

AI.prototype.bid = function (hand, currentHighBid, mustBid) {
    return this.strategy.bid(hand, currentHighBid, mustBid, this.playerId);
};

AI.prototype.chooseCard = function (hand, trick, trump, gameState) {
    var valid = hand.validPlays(trick.ledSuit, trump);
    var chosen = this.strategy.chooseCard(hand, trick, trump, gameState);
    // Safety: ensure chosen card is valid
    var isValid = valid.some(function (c) { return c.id === chosen.id; });
    return isValid ? chosen : valid[0];
};

// Factory — add new cases here to support higher AI levels later
AI.createStrategy = function (level) {
    switch (level) {
        case 1:
        default:
            return new AIStrategyLevel1();
    }
};

// ============================================================
// Game (state machine)
// ============================================================
function KlaverjasGame(aiLevel) {
    this.aiLevel    = aiLevel || 1;
    this.scores     = { wij: 0, zij: 0 };
    this.handNumber = 0;
    this.dealerIdx  = Math.floor(Math.random() * 4); // random first dealer
    this.phase      = PHASE.IDLE;
    this.gameId     = Date.now();

    this._handLog     = [];
    this._callbacks   = {};
    this._hands       = {};
    this._trickLog    = [];
    this._trickScores = { wij: 0, zij: 0 };
    this._roemScores  = { wij: 0, zij: 0 };
    this._tricks      = 0;
    this._pitTeam     = null;
    this._currentTrick = null;

    this.trump     = null;
    this.bidder    = null;
    this.bidAmount = null;
    this._dealer   = null;
    this._currentBid = null;

    this._biddingOrder = [];
    this._biddingIdx   = 0;
    this._awaitingHumanBid  = false;
    this._awaitingHumanPlay = false;
    this._trickPlayIdx      = 0;
    this._trickPlayerOrder  = [];

    this.ai = {
        'West':  new AI('West',  this.aiLevel),
        'Noord': new AI('Noord', this.aiLevel),
        'Oost':  new AI('Oost',  this.aiLevel)
    };
}

// --- Event system ---
KlaverjasGame.prototype.on = function (event, cb) {
    if (!this._callbacks[event]) this._callbacks[event] = [];
    this._callbacks[event].push(cb);
};

KlaverjasGame.prototype.emit = function (event) {
    var args = Array.prototype.slice.call(arguments, 1);
    var cbs  = this._callbacks[event] || [];
    for (var i = 0; i < cbs.length; i++) cbs[i].apply(null, args);
};

Object.defineProperty(KlaverjasGame.prototype, 'state', {
    get: function () {
        return {
            phase:      this.phase,
            scores:     { wij: this.scores.wij, zij: this.scores.zij },
            handNumber: this.handNumber,
            trump:      this.trump,
            bidder:     this.bidder,
            bidAmount:  this.bidAmount,
            dealer:     this._dealer,
            gameId:     this.gameId,
            currentBid: this._currentBid
        };
    }
});

KlaverjasGame.prototype.getHand = function (seat) { return this._hands[seat]; };

// --- Start a new hand ---
KlaverjasGame.prototype.startHand = function () {
    this.handNumber++;
    this.trump      = null;
    this.bidder     = null;
    this.bidAmount  = null;
    this._tricks    = 0;
    this._pitTeam   = null;
    this._trickScores = { wij: 0, zij: 0 };
    this._roemScores  = { wij: 0, zij: 0 };
    this._trickLog  = [];
    this._currentBid = null;
    this._awaitingHumanBid  = false;
    this._awaitingHumanPlay = false;

    this._dealer = SEAT_ORDER[this.dealerIdx % 4];
    this.dealerIdx++;

    this.phase = PHASE.DEALING;
    this.emit('phaseChange', PHASE.DEALING, { dealer: this._dealer });

    var deck = new Deck();
    deck.shuffle();
    var dealtCards = deck.deal(4, 8);
    for (var i = 0; i < SEAT_ORDER.length; i++) {
        this._hands[SEAT_ORDER[i]] = new Hand(dealtCards[i], SEAT_ORDER[i]);
    }

    this.emit('handsDealt', this._hands, this._dealer);

    var self = this;
    setTimeout(function () { self._startBidding(); }, 400);
};

// --- Bidding ---
KlaverjasGame.prototype._startBidding = function () {
    this.phase = PHASE.BIDDING;
    this.emit('phaseChange', PHASE.BIDDING);

    // Bidding order starts left of dealer
    var dealerIdx = SEAT_ORDER.indexOf(this._dealer);
    this._biddingOrder = [];
    for (var i = 1; i <= 4; i++) {
        this._biddingOrder.push(SEAT_ORDER[(dealerIdx + i) % 4]);
    }
    this._biddingIdx = 0;
    this._processBidding();
};

KlaverjasGame.prototype._processBidding = function () {
    if (this._biddingIdx >= this._biddingOrder.length) {
        this._finalizeBidding();
        return;
    }

    var seat = this._biddingOrder[this._biddingIdx];
    var isLastPlayer = this._biddingIdx === this._biddingOrder.length - 1;
    var mustBid = isLastPlayer && seat === this._dealer && !this._currentBid;

    this.emit('bidTurn', seat, this._currentBid, mustBid);

    if (seat === 'Zuid') {
        this._awaitingHumanBid = true;
        return; // UI will call humanBid()
    }

    var self = this;
    setTimeout(function () {
        var bid = self.ai[seat].bid(self._hands[seat], self._currentBid, mustBid);
        self._recordBid(seat, bid);
    }, DELAY_AI_BID);
};

KlaverjasGame.prototype._recordBid = function (seat, bid) {
    if (bid && (!this._currentBid || bid.amount > this._currentBid.amount)) {
        this._currentBid = { seat: seat, suit: bid.suit, amount: bid.amount };
    }
    this.emit('bidMade', seat, bid, this._currentBid);
    this._biddingIdx++;
    this._processBidding();
};

KlaverjasGame.prototype.humanBid = function (suit, amount) {
    if (!this._awaitingHumanBid) return;

    if (amount === 0) {
        // Pass — check if Zuid is forced to bid (last player, dealer, all passed)
        var isLast   = this._biddingIdx === this._biddingOrder.length - 1;
        var isDealer = this._dealer === 'Zuid';
        if (isLast && isDealer && !this._currentBid) {
            // Cannot pass — emit event so UI disables the Pass button
            this.emit('forcedBid', 'Zuid');
            return; // keep _awaitingHumanBid = true
        }
        this._awaitingHumanBid = false;
        this._recordBid('Zuid', null);
    } else {
        if (!suit || amount < 70) return;
        var minBid = this._currentBid ? this._currentBid.amount + 10 : 70;
        if (amount < minBid) return; // invalid — must outbid
        this._awaitingHumanBid = false;
        this._recordBid('Zuid', { suit: suit, amount: amount });
    }
};

KlaverjasGame.prototype._finalizeBidding = function () {
    if (!this._currentBid) {
        // Fallback: everyone passed (shouldn't normally happen)
        var rndSuit = SUITS[Math.floor(Math.random() * 4)];
        this._currentBid = { seat: this._dealer, suit: rndSuit, amount: 70 };
    }

    this.trump     = this._currentBid.suit;
    this.bidder    = this._currentBid.seat;
    this.bidAmount = this._currentBid.amount;

    this.emit('trumpSet', this.trump, this.bidder, this.bidAmount);

    var self = this;
    // Amsterdamse regels: geen roem-aankondiging voor het spel, direct starten
    setTimeout(function () { self.startPlaying(); }, 600);
};

// --- Roem ---
KlaverjasGame.prototype._startRoem = function () {
    this.phase = PHASE.ROEM;
    this.emit('phaseChange', PHASE.ROEM);

    var self  = this;
    var delay = 0;
    for (var i = 0; i < SEAT_ORDER.length; i++) {
        (function (seat) {
            setTimeout(function () {
                var items = self._hands[seat].detectRoem(self.trump);
                var team  = WIJ_TEAM.indexOf(seat) !== -1 ? 'wij' : 'zij';
                var total = items.reduce(function (s, r) { return s + r.points; }, 0);
                self._roemScores[team] += total;
                self.emit('roemReveal', seat, items, total);
            }, delay);
        })(SEAT_ORDER[i]);
        delay += DELAY_ROEM_STEP;
    }

    setTimeout(function () {
        self.emit('roemAllRevealed', self._roemScores);
    }, delay + 100);
};

// Called by UI after the player acknowledges the roem display
KlaverjasGame.prototype.startPlaying = function () {
    this.phase = PHASE.PLAYING;
    this.emit('phaseChange', PHASE.PLAYING);
    this._startTrick(this.bidder);
};

// --- Trick-taking ---
KlaverjasGame.prototype._startTrick = function (leaderId) {
    this._currentTrick = new Trick(leaderId, this.trump);

    var leaderIdx = SEAT_ORDER.indexOf(leaderId);
    this._trickPlayerOrder = [];
    for (var i = 0; i < 4; i++) {
        this._trickPlayerOrder.push(SEAT_ORDER[(leaderIdx + i) % 4]);
    }
    this._trickPlayIdx = 0;

    this.emit('trickStart', leaderId, this._tricks + 1);
    this._playNextCard();
};

KlaverjasGame.prototype._playNextCard = function () {
    if (this._trickPlayIdx >= 4) {
        this._endTrick();
        return;
    }

    var seat  = this._trickPlayerOrder[this._trickPlayIdx];
    var hand  = this._hands[seat];
    var valid = hand.validPlays(this._currentTrick.ledSuit, this.trump);

    this.emit('playerTurn', seat, valid);

    if (seat === 'Zuid') {
        this._awaitingHumanPlay = true;
        return; // UI calls humanPlay()
    }

    var self = this;
    setTimeout(function () {
        var card = self.ai[seat].chooseCard(self._hands[seat], self._currentTrick, self.trump, self.state);
        self._doPlayCard(seat, card);
    }, DELAY_AI_PLAY);
};

KlaverjasGame.prototype._doPlayCard = function (seat, card) {
    this._hands[seat].remove(card);
    this._currentTrick.addPlay(seat, card);
    this.emit('cardPlayed', seat, card, this._currentTrick.plays);
    this._trickPlayIdx++;
    var self = this;
    setTimeout(function () { self._playNextCard(); }, 250);
};

KlaverjasGame.prototype.humanPlay = function (card) {
    if (!this._awaitingHumanPlay) return;

    var valid = this._hands['Zuid'].validPlays(this._currentTrick.ledSuit, this.trump);
    var isValid = valid.some(function (c) { return c.id === card.id; });
    if (!isValid) return;

    this._awaitingHumanPlay = false;
    this._doPlayCard('Zuid', card);
};

KlaverjasGame.prototype._endTrick = function () {
    var winner  = this._currentTrick.winner;
    var pts     = this._currentTrick.points;
    this._tricks++;

    var isLast = this._tricks === 8;
    var bonus  = isLast ? 10 : 0;
    var total  = pts + bonus;

    var team = WIJ_TEAM.indexOf(winner) !== -1 ? 'wij' : 'zij';
    this._trickScores[team] += total;

    // Roem op tafel — winnaar van de slag krijgt de roempunten
    var roemItems = detectTrickRoem(this._currentTrick.plays, this.trump);
    var roemPts = roemItems.reduce(function (s, r) { return s + r.points; }, 0);
    if (roemPts > 0) this._roemScores[team] += roemPts;

    this._trickLog.push({ trickNumber: this._tricks, winner: winner, points: total, team: team });

    this.emit('trickWon', winner, this._currentTrick, total, this._tricks, roemItems);

    if (isLast) {
        // Check if one team won all 8 tricks (Pit)
        var allWij = this._trickLog.every(function (t) { return t.team === 'wij'; });
        var allZij = this._trickLog.every(function (t) { return t.team === 'zij'; });
        this._pitTeam = allWij ? 'wij' : (allZij ? 'zij' : null);

        var self = this;
        setTimeout(function () { self._endHand(); }, DELAY_TRICK_END);
    } else {
        var self = this;
        setTimeout(function () { self._startTrick(winner); }, DELAY_TRICK_END);
    }
};

KlaverjasGame.prototype._endHand = function () {
    this.phase = PHASE.SCORING;

    var bidderTeam = WIJ_TEAM.indexOf(this.bidder) !== -1 ? 'wij' : 'zij';

    var pointsWij = this._trickScores.wij;
    var pointsZij = this._trickScores.zij;
    var roemWij   = this._roemScores.wij;
    var roemZij   = this._roemScores.zij;

    // Pit bonus (+100 roem) goes to whichever team won ALL 8 tricks
    var isPit = false;
    if (this._pitTeam) {
        isPit = true;
        if (this._pitTeam === 'wij') roemWij += 100;
        else roemZij += 100;
    }

    // Did the bidding team meet their bid?
    var bidderTotal = bidderTeam === 'wij' ? (pointsWij + roemWij) : (pointsZij + roemZij);
    var bidMet = bidderTotal >= this.bidAmount;

    var result, finalWij, finalZij;

    if (!bidMet) {
        // Nat: all points + roem go to the opposing team
        result = 'N';
        var totalAll = pointsWij + pointsZij + roemWij + roemZij;
        finalWij = bidderTeam === 'wij' ? 0 : totalAll;
        finalZij = bidderTeam === 'zij' ? 0 : totalAll;
    } else if (isPit && this._pitTeam === bidderTeam) {
        result = 'P';
        finalWij = pointsWij + roemWij;
        finalZij = pointsZij + roemZij;
    } else {
        result = 'G';
        finalWij = pointsWij + roemWij;
        finalZij = pointsZij + roemZij;
    }

    this.scores.wij += finalWij;
    this.scores.zij += finalZij;

    var handResult = {
        handNumber: this.handNumber,
        bidder:     this.bidder,
        bidderTeam: bidderTeam,
        trump:      this.trump,
        bidAmount:  this.bidAmount,
        pointsWij:  pointsWij,
        pointsZij:  pointsZij,
        roemWij:    roemWij,
        roemZij:    roemZij,
        finalWij:   finalWij,
        finalZij:   finalZij,
        totalWij:   this.scores.wij,
        totalZij:   this.scores.zij,
        result:     result,
        isPit:      isPit,
        bidMet:     bidMet
    };

    this._handLog.push(handResult);
    this.emit('handComplete', handResult);
};

// Called by UI to start the next hand after the player has seen the result
KlaverjasGame.prototype.acknowledgeHand = function () {
    if (this.phase === PHASE.SCORING &&
        this.scores.wij < 1500 && this.scores.zij < 1500) {
        this.startHand();
    }
};

KlaverjasGame.prototype.resetGame = function () {
    this.scores     = { wij: 0, zij: 0 };
    this.handNumber = 0;
    this.dealerIdx  = Math.floor(Math.random() * 4);
    this.gameId     = Date.now();
    this._handLog   = [];
    this.phase      = PHASE.IDLE;
};

// ============================================================
// UI
// ============================================================
function KlaverjasUI(game) {
    this.game    = game;
    this._valid  = []; // valid cards for human this turn
    this._selectedBidSuit   = null;
    this._selectedBidAmount = null;

    this._bindGameEvents();
    this._bindUserEvents();
    this._syncDarkIcon();
}

KlaverjasUI.prototype._bindGameEvents = function () {
    var self = this;
    var g = this.game;

    g.on('phaseChange',     function (p, d)             { self._onPhaseChange(p, d); });
    g.on('handsDealt',      function (hands, dealer)     { self._onHandsDealt(hands, dealer); });
    g.on('bidTurn',         function (seat, cur, must)   { self._onBidTurn(seat, cur, must); });
    g.on('bidMade',         function (seat, bid, cur)    { self._onBidMade(seat, bid, cur); });
    g.on('forcedBid',       function (seat)              { self._onForcedBid(seat); });
    g.on('trumpSet',        function (trump, bidder, amt){ self._onTrumpSet(trump, bidder, amt); });
    g.on('roemReveal',      function (seat, items, tot)  { self._onRoemReveal(seat, items, tot); });
    g.on('roemAllRevealed', function (roem)              { self._onRoemAllRevealed(roem); });
    g.on('trickStart',      function (leader, nr)        { self._onTrickStart(leader, nr); });
    g.on('playerTurn',      function (seat, valid)       { self._onPlayerTurn(seat, valid); });
    g.on('cardPlayed',      function (seat, card, plays) { self._onCardPlayed(seat, card, plays); });
    g.on('trickWon',        function (w, trick, pts, nr, roem) { self._onTrickWon(w, trick, pts, nr, roem); });
    g.on('handComplete',    function (result)            { self._onHandComplete(result); });
    g.on('gameOver',        function (scores, log)       { self._onGameOver(scores, log); });
};

KlaverjasUI.prototype._bindUserEvents = function () {
    var self = this;

    // Suit selection
    $(document).on('click', '.bid-suit-btn', function () {
        $('.bid-suit-btn').removeClass('btn-primary').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-primary');
        self._selectedBidSuit = $(this).data('suit');
        self._updateBidConfirm();
    });

    // Amount selection
    $(document).on('click', '.bid-amt-btn:not([disabled])', function () {
        $('.bid-amt-btn').removeClass('btn-primary').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-primary');
        self._selectedBidAmount = parseInt($(this).data('amount'), 10);
        self._updateBidConfirm();
    });

    // Bid confirm
    $('#bid-confirm').on('click', function () {
        if (!$(this).prop('disabled')) {
            self.game.humanBid(self._selectedBidSuit, self._selectedBidAmount);
            self._resetBidSelection();
        }
    });

    // Bid pass
    $('#bid-pass').on('click', function () {
        if (!$(this).prop('disabled')) {
            self.game.humanBid(null, 0);
            self._resetBidSelection();
        }
    });

    // Roem OK → start playing
    $('#roem-ok').on('click', function () {
        $('#roem-panel').hide();
        self.game.startPlaying();
    });

    // Human plays a card
    $(document).on('click', '.card-playable', function () {
        if (!self.game._awaitingHumanPlay) return;

        var cardId  = $(this).data('card-id');
        var hand    = self.game.getHand('Zuid');
        var allCards = hand.cards;
        var card = null;
        for (var i = 0; i < allCards.length; i++) {
            if (allCards[i].id === cardId) { card = allCards[i]; break; }
        }
        if (!card) return;

        // Controleer verzaken: speler heeft het aangelegde symbool maar speelt iets anders
        var trick = self.game._currentTrick;
        if (trick && trick.ledSuit && card.suit !== trick.ledSuit && hand.hasSuit(trick.ledSuit)) {
            self._addMessage(
                '<strong>Verzaakt!</strong> Je hebt ' + SUIT_NAMES[trick.ledSuit] +
                ' ' + SUIT_SYMBOLS[trick.ledSuit] + ' — je moet die kleur spelen. Kies opnieuw.',
                'danger'
            );
            return;
        }

        self.game.humanPlay(card);
    });

    // Volgende hand knop
    $('#next-hand-btn').on('click', function () {
        $('#next-hand-panel').hide();
        self.game.startHand();
    });

    // Reset sessie (inline)
    $('#new-game-btn-inline').on('click', function () {
        $('#next-hand-panel').hide();
        $('#hand-history-body').empty();
        $('#play-messages').empty().hide();
        $('#roem-list').empty();
        self.game.resetGame();
        setTimeout(function () { self.game.startHand(); }, 400);
    });

    // Nieuwe sessie knop
    $('#new-game-btn').on('click', function () {
        $('#gameover-modal').modal('hide');
        $('#next-hand-panel').hide();
        $('#hand-history-body').empty();
        $('#play-messages').empty().hide();
        $('#roem-list').empty();
        self.game.resetGame();
        setTimeout(function () { self.game.startHand(); }, 400);
    });
};

// ---- Game event handlers ----

KlaverjasUI.prototype._onPhaseChange = function (phase) {
    if (phase !== PHASE.BIDDING) $('#bidding-panel').hide();
    if (phase !== PHASE.ROEM)    $('#roem-panel').hide();
    if (phase === PHASE.DEALING) {
        $('#roem-list').empty();
        this._clearTrickArea();
    }
};

KlaverjasUI.prototype._onHandsDealt = function (hands, dealer) {
    $('#ui-hand-nr').text(this.game.handNumber);
    $('#ui-trump').html('&mdash;');
    $('#ui-bid-display').html('&mdash;');
    $('#ui-dealer').text(dealer);

    // Render AI hands (card backs)
    ['West', 'Noord', 'Oost'].forEach(function (seat) {
        var el = $('#hand-' + seat.toLowerCase()).empty();
        var count = hands[seat].size;
        for (var i = 0; i < count; i++) {
            el.append($('<img>').addClass('card-img card-img-back')
                .attr('src', CARD_IMG_PATH + 'back.svg').attr('alt', '?'));
        }
    });

    this._renderHumanHand(hands['Zuid'].cards);
};

KlaverjasUI.prototype._renderHumanHand = function (cards) {
    // Sorteer op symbool (H, D, S, C) dan hoog naar laag binnen elk symbool
    var SUIT_DISPLAY_ORDER = { 'H': 0, 'D': 1, 'S': 2, 'C': 3 };
    var sorted = cards.slice().sort(function (a, b) {
        if (a.suit !== b.suit) return SUIT_DISPLAY_ORDER[a.suit] - SUIT_DISPLAY_ORDER[b.suit];
        return SEQ_ORDER.indexOf(b.rank) - SEQ_ORDER.indexOf(a.rank);
    });
    var el = $('#hand-zuid').empty();
    for (var i = 0; i < sorted.length; i++) {
        var card = sorted[i];
        el.append(
            $('<img>')
                .addClass('card-img card-playable')
                .attr('src', card.imgSrc)
                .attr('alt', card.label)
                .attr('data-card-id', card.id)
                .attr('title', card.label)
        );
    }
};

KlaverjasUI.prototype._onBidTurn = function (seat, currentBid, mustBid) {
    var curText = currentBid
        ? 'Huidig bod: <strong>' + currentBid.amount +
          ' ' + SUIT_SYMBOLS[currentBid.suit] +
          '</strong> (' + currentBid.seat + ')'
        : 'Nog geen bod';
    $('#bid-status').html(curText);

    if (seat === 'Zuid') {
        $('#bid-current-player').text(mustBid ? 'Jouw beurt (deler — moet bieden!)' : 'Jouw beurt');
        this._renderBidAmounts(currentBid ? currentBid.amount : 60);
        $('#bid-pass').prop('disabled', mustBid); // can't pass if forced
        $('#bidding-panel').show();
    } else {
        $('#bid-current-player').text(seat + ' denkt na…');
        $('#bid-confirm, #bid-pass').prop('disabled', true);
        if (!$('#bidding-panel').is(':visible')) $('#bidding-panel').show();
    }
};

KlaverjasUI.prototype._onForcedBid = function () {
    $('#bid-current-player').text('Jij bent deler — je moet bieden!');
    $('#bid-pass').prop('disabled', true);
    this._renderBidAmounts(60); // all amounts enabled
};

KlaverjasUI.prototype._onBidMade = function (seat, bid) {
    if (bid) {
        this._addMessage(seat + ' biedt ' + bid.amount + ' ' + SUIT_SYMBOLS[bid.suit], 'info');
    } else {
        this._addMessage(seat + ' past', 'info');
    }
};

KlaverjasUI.prototype._onTrumpSet = function (trump, bidder, amount) {
    var label = amount === 162 ? 'Pit' : amount;
    $('#ui-trump').html(SUIT_NAMES[trump] + ' ' + SUIT_SYMBOLS[trump]);
    $('#ui-bid-display').text(bidder + ': ' + label);
    $('#bidding-panel').hide();
    this._addMessage(
        bidder + ' speelt <strong>' + amount + ' ' + SUIT_NAMES[trump] + ' ' + SUIT_SYMBOLS[trump] + '</strong>',
        'success'
    );
};

KlaverjasUI.prototype._onRoemReveal = function (seat, items, total) {
    if (total === 0) return;
    var desc = items.map(function (r) { return r.description + ' (' + r.points + ' pt)'; }).join(', ');
    $('#roem-list').append(
        $('<li>').html('<strong>' + seat + '</strong>: ' + desc + ' = ' + total + ' pt')
    );
    $('#roem-panel').show();
};

KlaverjasUI.prototype._onRoemAllRevealed = function () {
    if ($('#roem-list').children().length === 0) {
        // No roem at all — skip roem panel
        this.game.startPlaying();
    }
    // Otherwise wait for user to click "Verder"
};

KlaverjasUI.prototype._onTrickStart = function () {
    this._clearTrickArea();
};

KlaverjasUI.prototype._onPlayerTurn = function (seat, valid) {
    if (seat === 'Zuid') {
        this._valid = valid; // bewaard voor eventuele referentie
        this._renderHumanHand(this.game.getHand('Zuid').cards);
    }
};

KlaverjasUI.prototype._onCardPlayed = function (seat, card) {
    var slotId = '#trick-' + seat.toLowerCase();
    $(slotId).empty().append(
        $('<img>').addClass('card-img').attr('src', card.imgSrc).attr('alt', card.label)
    );

    if (seat === 'Zuid') {
        this._valid = [];
        this._renderHumanHand(this.game.getHand('Zuid').cards);
    } else {
        // Remove one card back from AI hand display
        $('#hand-' + seat.toLowerCase()).find('.card-img-back:last').remove();
    }
};

KlaverjasUI.prototype._onTrickWon = function (winner, trick, pts, trickNr, roemItems) {
    var roemPts = (roemItems || []).reduce(function (s, r) { return s + r.points; }, 0);
    var msg = 'Slag ' + trickNr + ': <strong>' + winner + '</strong> wint (' + pts + ' pt)';
    if (roemPts > 0) {
        var desc = (roemItems || []).map(function (r) { return r.description + ' +' + r.points; }).join(', ');
        msg += ' + <strong>roem: ' + roemPts + ' pt</strong> (' + desc + ')';
    }
    this._addMessage(msg, 'info');
    var seatEl = $('#seat-' + winner.toLowerCase());
    seatEl.addClass('seat-winning');
    setTimeout(function () { seatEl.removeClass('seat-winning'); }, DELAY_TRICK_END - 300);
};

KlaverjasUI.prototype._onHandComplete = function (result) {
    var label = result.result === 'P' ? 'Pit ★' : (result.result === 'N' ? 'Nat' : 'Gehaald');
    var rowCls = result.result === 'N' ? 'danger' : (result.result === 'P' ? 'warning' : 'success');

    $('#hand-history-body').append(
        $('<tr>').addClass(rowCls)
            .append($('<td>').text(result.handNumber))
            .append($('<td>').text(result.bidder))
            .append($('<td>').text(result.bidAmount === 162 ? 'Pit' : result.bidAmount))
            .append($('<td>').text(SUIT_SYMBOLS[result.trump]))
            .append($('<td>').text(result.finalWij))
            .append($('<td>').text(result.roemWij))
            .append($('<td>').text(result.finalZij))
            .append($('<td>').text(result.roemZij))
            .append($('<td>').html('<strong>' + label + '</strong>'))
    );

    $('#ui-score-wij').text(result.totalWij);
    $('#ui-score-zij').text(result.totalZij);

    var msgType = result.result === 'N' ? 'danger' : 'success';
    this._addMessage(
        'Hand ' + result.handNumber + ': <strong>' + label +
        '</strong> — Wij: ' + result.totalWij + ', Zij: ' + result.totalZij,
        msgType
    );

    // Sla deze hand op in de database
    $.post('config/play-save.php', {
        '_token':      window.BOERNEL_TOKEN,
        'game_id':     this.game.gameId,
        'hands':       1,
        'score_wij':   result.finalWij,
        'score_zij':   result.finalZij,
        'ai_level':    this.game.aiLevel,
        'detail_json': JSON.stringify(result)
    });

    // Toon knop voor volgende hand
    $('#next-hand-panel').show();
};

KlaverjasUI.prototype._onGameOver = function (scores, handLog) {
    var wijWon = scores.wij > scores.zij;
    $('#gameover-title').text(wijWon ? 'Gewonnen! 🎉' : 'Verloren…');
    $('#gameover-body').html(
        '<p>Eindstand: <strong>Wij ' + scores.wij + '</strong> — Zij ' + scores.zij + '</p>' +
        '<p>Gespeeld in ' + handLog.length + ' handen.</p>'
    );

    $.post('config/play-save.php', {
        '_token':      window.BOERNEL_TOKEN,
        'game_id':     this.game.gameId,
        'hands':       handLog.length,
        'score_wij':   scores.wij,
        'score_zij':   scores.zij,
        'ai_level':    this.game.aiLevel,
        'detail_json': JSON.stringify(handLog)
    }).always(function () {
        $('#gameover-modal').modal('show');
    });
};

// ---- Bidding UI helpers ----

KlaverjasUI.prototype._renderBidAmounts = function (minAmount) {
    var row = $('#bid-amount-row').empty();
    BID_STEPS.forEach(function (amt) {
        var btn = $('<button>')
            .addClass('btn btn-sm bid-amt-btn btn-default')
            .attr('data-amount', amt)
            .text(amt === 162 ? 'Pit' : String(amt));
        if (amt <= minAmount) btn.prop('disabled', true).addClass('disabled');
        row.append(btn);
    });
    this._selectedBidAmount = null;
    this._selectedBidSuit   = null;
};

KlaverjasUI.prototype._updateBidConfirm = function () {
    $('#bid-confirm').prop('disabled', !(this._selectedBidSuit && this._selectedBidAmount));
};

KlaverjasUI.prototype._resetBidSelection = function () {
    this._selectedBidSuit   = null;
    this._selectedBidAmount = null;
    $('.bid-suit-btn, .bid-amt-btn').removeClass('btn-primary').addClass('btn-default');
    $('#bid-confirm').prop('disabled', true);
};

// ---- General helpers ----

KlaverjasUI.prototype._clearTrickArea = function () {
    $('#trick-west, #trick-noord, #trick-oost, #trick-zuid').empty();
};

KlaverjasUI.prototype._addMessage = function (html, type) {
    var cls = type === 'success' ? 'alert-success' :
              (type === 'danger'  ? 'alert-danger'  : 'alert-info');
    var msg = $('<div>').addClass('alert ' + cls + ' play-msg').html(html);
    $('#play-messages').show().prepend(msg);
    setTimeout(function () { msg.fadeOut(400, function () { msg.remove(); }); }, 5000);
};

KlaverjasUI.prototype._syncDarkIcon = function () {
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    $('#darkIcon').attr('class', isDark ? 'fas fa-sun' : 'fas fa-moon');
};

// ============================================================
// Expose to global scope for play.php initialization
// ============================================================
window.KlaverjasGame = KlaverjasGame;
window.KlaverjasUI   = KlaverjasUI;

})();
