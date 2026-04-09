<?php
require_once __DIR__ . '/config/auth.php';
// auth.php defines API_TOKEN — used for BOERNEL_TOKEN injection below.
// requireToken() is NOT called here (this is a GET page, not an API endpoint).
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Klaverjassen — Solo vs AI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

    <!-- Dark mode flash prevention — must run before CSS renders -->
    <script>(function(){var t=localStorage.getItem('boernel-theme');if(!t&&window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)t='dark';if(t==='dark')document.documentElement.setAttribute('data-theme','dark');})();</script>

    <link href="./bestanden/bootstrap.min.css" rel="stylesheet">
    <link href="./bestanden/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <link href="./bestanden/bernard.css" rel="stylesheet">

    <style>
        /* ---- Game table ---- */
        #game-table {
            position: relative;
            width: 100%;
            max-width: 560px;
            margin: 10px auto;
            aspect-ratio: 1 / 1;
            background: #2d6a2d;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        }
        [data-theme="dark"] #game-table { background: #1a4d1a; }

        /* ---- Seat containers (absolute inside game-table) ---- */
        .seat {
            position: absolute;
            text-align: center;
        }
        #seat-noord {
            top: 6px; left: 50%;
            transform: translateX(-50%);
            width: 45%;
        }
        #seat-west {
            left: 4px; top: 50%;
            transform: translateY(-50%);
            width: 18%;
        }
        #seat-oost {
            right: 4px; top: 50%;
            transform: translateY(-50%);
            width: 18%;
        }
        #seat-zuid {
            bottom: 6px; left: 50%;
            transform: translateX(-50%);
            width: 60%;
        }

        /* ---- Trick area (center of table) ---- */
        #trick-area {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 200px; height: 160px;
        }
        .trick-slot {
            position: absolute;
        }
        #trick-noord { top: 0;    left: 50%; transform: translateX(-50%); }
        #trick-west  { left: 0;  top: 50%;  transform: translateY(-50%); }
        #trick-oost  { right: 0; top: 50%;  transform: translateY(-50%); }
        #trick-zuid  { bottom: 0; left: 50%; transform: translateX(-50%); }

        /* ---- Card images ---- */
        .card-img {
            width: 52px;
            border-radius: 4px;
            box-shadow: 1px 2px 5px rgba(0,0,0,0.45);
            margin: 1px;
            display: inline-block;
            vertical-align: bottom;
        }
        /* Human hand — lift playable cards on hover */
        .card-playable {
            cursor: pointer;
            transition: transform 0.12s;
        }
        .card-playable:hover {
            transform: translateY(-10px);
        }
        .card-invalid {
            opacity: 0.42;
            cursor: not-allowed;
        }

        /* AI hands — vertical stacking for West/Oost */
        #hand-west .card-img,
        #hand-oost .card-img {
            display: block;
            margin: -38px auto 0;
        }
        #hand-west .card-img:first-child,
        #hand-oost .card-img:first-child {
            margin-top: 0;
        }

        /* Noord hand — horizontal but compact */
        #hand-noord .card-img {
            margin: 1px -4px;
        }

        /* Zuid hand — horizontal, spread out */
        #hand-zuid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2px;
        }

        /* Seat labels */
        .seat-label {
            color: rgba(255,255,255,0.85);
            font-size: 11px;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.6);
            margin-bottom: 2px;
        }

        /* Winning seat highlight */
        .seat-winning .seat-label {
            color: #ffe066;
        }

        /* ---- Status bar ---- */
        #status-bar {
            font-size: 13px;
            padding: 6px 10px;
            margin-bottom: 6px;
        }

        /* ---- Bidding panel ---- */
        #bidding-panel {
            margin-top: 8px;
        }
        #bid-amount-row {
            margin: 6px 0;
        }
        #bid-amount-row .btn {
            margin: 2px;
            min-width: 42px;
        }
        #bid-suit-row .btn {
            margin: 2px 4px;
        }
        .bid-suit-btn[data-suit="H"],
        .bid-suit-btn[data-suit="D"] { color: #cc0000; }

        /* ---- Roem panel ---- */
        #roem-panel ul { list-style: none; padding: 0; margin: 6px 0; }
        #roem-panel li { padding: 2px 0; }

        /* ---- Hand history ---- */
        #hand-history {
            max-height: 220px;
            overflow-y: auto;
            margin-top: 8px;
        }
        #hand-history th, #hand-history td {
            font-size: 12px;
            padding: 3px 5px;
        }

        /* ---- Messages ---- */
        #play-messages .play-msg {
            padding: 5px 10px;
            font-size: 13px;
            margin-bottom: 4px;
        }
        #play-messages .close { display: none; } /* auto-dismiss, no manual close needed */

        /* ---- Score display ---- */
        #score-display {
            font-size: 16px;
            margin: 6px 0;
        }
        #score-display .score-wij { color: #2b7a2b; }
        #score-display .score-zij { color: #9b3333; }
        [data-theme="dark"] #score-display .score-wij { color: #66cc66; }
        [data-theme="dark"] #score-display .score-zij { color: #ff8080; }
    </style>
</head>
<body>

<div class="container-fluid">

    <!-- Header -->
    <div class="row" style="position:relative; margin-bottom:4px;">
        <div class="col-xs-12">
            <h2 style="margin:10px 0 4px;">
                Boer<span class="text-primary">Nel</span>.nl
                <small>Solo klaverjassen vs AI</small>
            </h2>
        </div>
        <div style="position:absolute; top:10px; right:15px;">
            <button class="btn btn-default" id="darkToggle" title="Wissel thema" onclick="toggleDark()">
                <i id="darkIcon" class="fas fa-moon"></i>
            </button>
        </div>
    </div>

    <!-- Score totals -->
    <div id="score-display" class="text-center">
        <span class="score-wij"><strong>Wij: <span id="ui-score-wij">0</span></strong></span>
        &nbsp;&nbsp;—&nbsp;&nbsp;
        <span class="score-zij"><strong>Zij: <span id="ui-score-zij">0</span></strong></span>
    </div>

    <!-- Status bar: hand info -->
    <div id="status-bar" class="well well-sm text-center">
        Hand <span id="ui-hand-nr">—</span>
        &nbsp;|&nbsp;
        Deler: <span id="ui-dealer">—</span>
        &nbsp;|&nbsp;
        Troef: <span id="ui-trump">—</span>
        &nbsp;|&nbsp;
        Bod: <span id="ui-bid-display">—</span>
    </div>

    <!-- The game table -->
    <div id="game-table">

        <!-- North: Maat (partner) -->
        <div id="seat-noord" class="seat">
            <div class="seat-label">Noord (Maat)</div>
            <div id="hand-noord"></div>
        </div>

        <!-- West: Opponent -->
        <div id="seat-west" class="seat">
            <div class="seat-label">West</div>
            <div id="hand-west"></div>
        </div>

        <!-- East: Opponent -->
        <div id="seat-oost" class="seat">
            <div class="seat-label">Oost</div>
            <div id="hand-oost"></div>
        </div>

        <!-- Trick area (center) -->
        <div id="trick-area">
            <div id="trick-noord" class="trick-slot"></div>
            <div id="trick-west"  class="trick-slot"></div>
            <div id="trick-oost"  class="trick-slot"></div>
            <div id="trick-zuid"  class="trick-slot"></div>
        </div>

        <!-- South: Human player -->
        <div id="seat-zuid" class="seat">
            <div id="hand-zuid"></div>
            <div class="seat-label" style="margin-top:4px;">Jij (Zuid)</div>
        </div>

    </div><!-- #game-table -->

    <!-- Bidding panel — shown only during bidding phase -->
    <div id="bidding-panel" class="well well-sm" style="display:none; margin-top:10px;">
        <h5 style="margin:0 0 6px;">
            Bieden &mdash; <span id="bid-current-player" class="text-primary"></span>
        </h5>
        <div id="bid-status" style="margin-bottom:6px; font-size:13px;"></div>

        <!-- Suit selection -->
        <div id="bid-suit-row">
            <button class="btn btn-default bid-suit-btn" data-suit="H">Harten ♥</button>
            <button class="btn btn-default bid-suit-btn" data-suit="S">Schoppen ♠</button>
            <button class="btn btn-default bid-suit-btn" data-suit="D">Ruiten ♦</button>
            <button class="btn btn-default bid-suit-btn" data-suit="C">Klaveren ♣</button>
        </div>

        <!-- Amount selection (populated by JS) -->
        <div id="bid-amount-row"></div>

        <div style="margin-top:8px;">
            <button id="bid-confirm" class="btn btn-success" disabled>Bied</button>
            <button id="bid-pass"    class="btn btn-warning">Pas</button>
        </div>
    </div>

    <!-- Roem reveal panel -->
    <div id="roem-panel" class="well well-sm" style="display:none; margin-top:10px;">
        <h5 style="margin:0 0 6px;">Roem</h5>
        <ul id="roem-list"></ul>
        <button id="roem-ok" class="btn btn-primary btn-sm">Verder ›</button>
    </div>

    <!-- Message feed -->
    <div id="play-messages" style="display:none; margin-top:8px; max-height:120px; overflow-y:auto;"></div>

    <!-- Hand history table -->
    <h5 style="margin-top:12px;">Hands</h5>
    <div id="hand-history">
        <table class="table table-condensed table-bordered table-striped">
            <thead>
                <tr class="active">
                    <th>#</th>
                    <th>Wie</th>
                    <th>Bod</th>
                    <th>Troef</th>
                    <th class="text-right">Wij</th>
                    <th class="text-right">Roem W</th>
                    <th class="text-left">Zij</th>
                    <th class="text-left">Roem Z</th>
                    <th>Uitslag</th>
                </tr>
            </thead>
            <tbody id="hand-history-body"></tbody>
        </table>
    </div>

    <!-- Game Over modal -->
    <div class="modal fade" id="gameover-modal" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="gameover-title"></h4>
                </div>
                <div class="modal-body" id="gameover-body"></div>
                <div class="modal-footer">
                    <button id="new-game-btn" class="btn btn-success btn-lg">Nieuw spel</button>
                    <a href="index.php" class="btn btn-default btn-lg">Home</a>
                </div>
            </div>
        </div>
    </div>

    <p class="footer text-center text-muted" style="margin-top:16px;">
        &copy; 2018&ndash;<?php echo date('Y'); ?> <a href="mailto:breinink@gmail.com">brein inc.</a>
    </p>

</div><!-- .container-fluid -->

<!-- JavaScript — same load order as scoreboard.php -->
<script src="./bestanden/jquery.min.js"></script>
<script src="./bestanden/bootstrap.min.js"></script>

<!-- API token for play-save.php (same pattern as scoreboard.php) -->
<script>var BOERNEL_TOKEN = '<?php echo API_TOKEN; ?>';</script>

<script src="./bestanden/play.js"></script>

<script>
/* Dark mode toggle — identical to other pages */
function toggleDark() {
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    document.documentElement.setAttribute('data-theme', isDark ? 'light' : 'dark');
    localStorage.setItem('boernel-theme', isDark ? 'light' : 'dark');
    document.getElementById('darkIcon').className = isDark ? 'fas fa-moon' : 'fas fa-sun';
}

$(document).ready(function () {
    // Sync dark icon on load
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    document.getElementById('darkIcon').className = isDark ? 'fas fa-sun' : 'fas fa-moon';

    // Boot the game
    var game = new KlaverjasGame(1); // Level 1 AI
    var ui   = new KlaverjasUI(game);
    game.startHand();
});
</script>

</body>
</html>
