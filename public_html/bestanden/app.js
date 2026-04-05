// const stuff (capitalized variable names)
var ROEM_SYMBOLS = new Array();
ROEM_SYMBOLS[20] = "I";
ROEM_SYMBOLS[50] = "V";
ROEM_SYMBOLS[100] = "X";
VOLGORDE = [1, 3, 2, 4]

// temp vars
var temp;
var currentStyle = 'standard';

// per-game state
var gamestats;
var totaalpuntenwij = 0;
var totaalpuntenzij = 0;
var totaalroemwij = 0;
var totaalroemzij = 0;
var totaalwij = 0;
var totaalzij = 0;
var round = 1;
var spel;
var spelers = ["Speler 1", "Speler 2", "Speler 3", "Speler 4", 0];
var selText = "";
var selSpeler = 0;
var kleuren = ["Harten", "Schoppen", "Ruiten", "Klaveren", "Sans"];
var verzaker = 0;
var puntentotaal = 162;
var deler1 = 0;
var deler = "";
var elo1 = 0;
var elo2 = 0;
var elo3 = 0;
var elo4 = 0;
var maatje = "";
var wil = 0;
var spelersoud = [];

// per-round state
var puntenwij = 0;
var puntenzij = 0;
var roemwij = 0;
var roemzij = 0;
var wijstatus = 0;
var zijstatus = 0;
var roemwij_list = new Array();
var roemzij_list = new Array();
var boernelDateId = 0;
var wie = 0;
var voor = 0;
var op = 0;
var team = 0;
var verzaker = 0;


// reset game state
function resetGame() {
	totaalpuntenwij = 0;
	totaalpuntenzij = 0;
	totaalroemwij = 0;
	totaalroemzij = 0;
	totaalwij = 0;
	totaalzij = 0;
	round = 1;
	spelers = ["Speler 1", "Speler 2", "Speler 3", "Speler 4"];
	$("#scorelist").empty();
	resetInputFields();
	enableAllButtons();
	$("#verwerk").html("Verwerk &raquo;");
	$("#resetbutton").fadeOut();
	gamestats = new Array(25);
	roemwij_list = new Array();
	roemzij_list = new Array();
	for (var i = 0; i < 25; i++) {
		gamestats[i] = new Array(11);
	}
	var d = new Date();
	boernelDateId = parseInt(d.getTime()); // milliseconds
	updateGameState();


} // resetGame

function checkspelers() {
	if(spelers[0] == "Speler 1" || spelers[1] == "Speler 2" || spelers[2] == "Speler 3" || spelers[3] == "Speler 4")
	{
	$("#klaar").attr("disabled", "disabled");
	$('#Spelerinput').modal('show');

	}
}

// reset current round state
function resetRound() {
	puntenwij = 0;
	puntenzij = 0;
	roemwij = 0;
	roemzij = 0;
	wijstatus = 0;
	zijstatus = 0;
	roemwij_list = new Array();
	roemzij_list = new Array();
	wie = 0;
	voor = 0;
	op = 0;
	team = 0;
	verzaker = 0;
} // resetRound

// show text in in alert field and fade after 2 secs
function myAlert(what) {
	$("#messages").html(what);
	$('#messages').fadeIn();
	setTimeout(function() {
		$('#messages').fadeOut();
	}, 5000);
}

function myAlert2(what) {
	$("#bericht").html("<span class=\"text-success\">" + what + "</span>");
	$('#bericht').fadeIn();
	setTimeout(function() {
		$('#bericht').fadeOut();
	}, 5000);
}

// show (green) success text
function successAlert(what) {
	myAlert("<span class=\"text-success\">" + what + "</span>");
}

// show (red) failure text
function errorAlert(what) {
	myAlert("<span class=\"text-danger\">" + what + "</span>");
}

function deler() {
deler = spelers[VOLGORDE[(round - 1 + VOLGORDE[deler1 - 1] - 1) % 4] - 1];
spelers[4] = deler1;
$("#dealer").html(deler);
}

// add row to table every-4-round subtotals
function showSubTotals() {
	$("#scorelist").append("<tr id=\"subtotaal" + round + "\" class=\"active\" style=\"display: none;\"><th class=\"text-right\">"
	+ totaalroemwij + "</th><th class=\"text-right\">"
	+ totaalpuntenwij + "</th><th class=\"text-center\">Totaal</th><th class=\"text-left\">"
	+ totaalpuntenzij + "</th><th class=\"text-left\">"
	+ totaalroemzij + "</th></tr>");
	$("#subtotaal" + round).fadeIn();
} // showSubTotals

// add row to table
function showTotals() {
	$("#verwerk").html("Einde!");
	$("#scorelist").append("<tr id=\"eindtotaal\" class=\"active\" style=\"display: none;\"><th class=\"text-right text-underline\">"
	+ totaalroemwij + "</th><th class=\"text-right text-underline\">"
	+ totaalpuntenwij + "</th><th class=\"text-center text-underline\">Eindtotaal</th><th class=\"text-left text-underline\">"
	+ totaalpuntenzij + "</th><th class=\"text-left text-underline\">"
	+ totaalroemzij + "</th></tr>");
	$("#eindtotaal").fadeIn();
} // showTotals

// enable all buttons
function enableAllButtons() {
	$("#verwerk").removeAttr("disabled");
	$("#puntenwij").removeAttr("disabled");
	$("#puntenzij").removeAttr("disabled");
	$("#wijpit").removeAttr("disabled");
	$("#zijpit").removeAttr("disabled");
	$("#wijnat").removeAttr("disabled");
	$("#zijnat").removeAttr("disabled");
	$("#wijverzaken").removeAttr("disabled");
	$("#zijverzaken").removeAttr("disabled");
	$("#undolastmove").removeAttr("disabled");
	$("#roemwijbuttons").children("button").removeAttr("disabled");
	$("#roemzijbuttons").children("button").removeAttr("disabled");
	$("#opwelkekleur").children("button").removeAttr("disabled");
	$("#voorhoeveel").children("button").attr("disabled", "disabled");
} // enableAllButtons

// disable all buttons
function disableAllButtons() {
	$("#verwerk").attr("disabled", "disabled");
	$("#puntenwij").attr("disabled", "disabled");
	$("#puntenzij").attr("disabled", "disabled");
	$("#wijpit").attr("disabled", "disabled");
	$("#zijpit").attr("disabled", "disabled");
	$("#wijnat").attr("disabled", "disabled");
	$("#zijnat").attr("disabled", "disabled");
	$("#wijverzaken").attr("disabled", "disabled");
	$("#zijverzaken").attr("disabled", "disabled");
	$("#undolastmove").attr("disabled", "disabled");
	$("#roemwijbuttons").children("button").attr("disabled", "disabled");
	$("#roemzijbuttons").children("button").attr("disabled", "disabled");
} // disableAllButtons

// reset visible round state (input fields)
function resetInputFields() {
	$("#roemwij").val("");
	$("#puntenwij").val("");
	$("#puntenzij").val("");
	$("#roemzij").val("");
	$("#wijpit").removeAttr("disabled");
	$("#zijpit").removeAttr("disabled");
	$('#opwelkekleur').children("button").removeClass('btn-primary').addClass('btn-default');
	$('#voorhoeveel').children("button").removeClass('btn-primary').addClass('btn-default ');
	$('#speler1').removeClass('btn-primary').addClass('btn-default');
	$('#speler2').removeClass('btn-primary').addClass('btn-default');
	$('#speler3').removeClass('btn-primary').addClass('btn-default');
	$('#speler4').removeClass('btn-primary').addClass('btn-default');
    }
// resetInputFields

// reset player buttons
function resetPlayerButtons() {
$('#Teller').removeClass('btn-success').addClass('btn-default');
$('#Maat').removeClass('btn-success').addClass('btn-default');
$('#West').removeClass('btn-success').addClass('btn-default');
$('#Oost').removeClass('btn-success').addClass('btn-default');
$("#speler1").text(spelers[0]);
$("#speler1V").text(spelers[0]);
$("#speler1D").text(spelers[0]);
$("#speler2").text(spelers[1]);
$("#speler2V").text(spelers[1]);
$("#speler2D").text(spelers[1]);
$("#speler3").text(spelers[2]);
$("#speler3V").text(spelers[2]);
$("#speler3D").text(spelers[2]);
$("#speler4").text(spelers[3]);
$("#speler4V").text(spelers[3]);
$("#speler4D").text(spelers[3]);
    }
// resetPlayerButtons



// update visible round state
function updateGameState() {


	$("#totaalwij").html(totaalwij);
	$("#totaalzij").html(totaalzij);

	if(round <= 50) {
	deler = spelers[VOLGORDE[(round - 1 + VOLGORDE[deler1 - 1] - 1) % 4] - 1];
	spelers[4] = deler1;
	$("#round").html(round);

	$("#dealer").html(deler);
	}
	textualdateId = boernelDateId.toString(36).toUpperCase().toLowerCase();
	$("#spelid").val(textualdateId);
} // updateGameState

// get dropdown text
$(function(){

  $(".dropdown-menu li a").click(function(){


    selText = $(this).html();
    spelers[selSpeler - 1] = selText

    if(selSpeler - 1 != 0) {
    if(selText == spelers[0]) {
    spelers[0] = "Speler 1"
    $('#Teller').removeClass('btn-success').addClass('btn-default');
		$("#Teller").text("Teller");
    }
    }

    if(selSpeler - 1 != 1) {
    if(selText == spelers[1]) {
    spelers[1] = "Speler 2"
    $('#Maat').removeClass('btn-success').addClass('btn-default');
		$("#Maat").text("Maat");
    }
    }

    if(selSpeler - 1 != 2) {
    if(selText == spelers[2]) {
    spelers[2] = "Speler 3"
    $('#West').removeClass('btn-success').addClass('btn-default');
		$("#West").text("West");
    }
    }

    if(selSpeler - 1 != 3) {
    if(selText == spelers[3]) {
    spelers[3] = "Speler 4"
    $('#Oost').removeClass('btn-success').addClass('btn-default');
		$("#Oost").text("Oost");
    }
    }

    $("#speler1").text(spelers[0]);
    $("#speler1V").text(spelers[0]);
		$("#speler1D").text(spelers[0]);
    if(spelers[0] != "Speler 1") {
    $('#Teller').removeClass('btn-default').addClass('btn-success');
		$("#Teller").text(spelers[0]);
		}
    $("#speler2").text(spelers[1]);
    $("#speler2V").text(spelers[1]);
		$("#speler2D").text(spelers[1]);
        if(spelers[1] != "Speler 2") {
    $('#Maat').removeClass('btn-default').addClass('btn-success');
		$("#Maat").text(spelers[1]);
    }
    $("#speler3").text(spelers[2]);
    $("#speler3V").text(spelers[2]);
		$("#speler3D").text(spelers[2]);
        if(spelers[2] != "Speler 3") {
    $('#West').removeClass('btn-default').addClass('btn-success');
		$("#West").text(spelers[2]);
    }
    $("#speler4").text(spelers[3]);
    $("#speler4V").text(spelers[3]);
			$("#speler4D").text(spelers[3]);
        if(spelers[3] != "Speler 4") {
    $('#Oost').removeClass('btn-default').addClass('btn-success');
		$("#Oost").text(spelers[3]);
    }
		if(spelers[0] != "Speler 1" && spelers[1] != "Speler 2" && spelers[2] != "Speler 3" && spelers[3] != "Speler 4")
		{
		$("#boeren").removeAttr("disabled");

	} else
	{
		$("#boeren").attr("disabled", "disabled");
	}

    //selText = $(".btn:first-child").text();
  });

});
// get dropdown text

// speler 1
function speler1() {
		selSpeler = 1
		}
// speler 1

// speler 2
function speler2() {
		selSpeler = 2
		}
// speler 2

// speler 3
function speler3() {
		selSpeler = 3
		}
// speler 3

// speler 4
function speler4() {
		selSpeler = 4
		}
// speler 4

// undo the last move
function undoLastMove() {

	if(round > 1) {

		round--;
		$.post("config/del.php", { 'round': round, 'dateId': boernelDateId }, function(data) {
  // use the result
  myalert(data);
});

		// fetch results of last round
		puntenwij = gamestats[round-1][0];
		roemwij = gamestats[round-1][1];
		puntenzij = gamestats[round-1][3];
		roemzij = gamestats[round-1][4];

		// reset game stats
		gamestats[round-1] = new Array(6);

		// update totals
		totaalroemwij -= roemwij;
		totaalroemzij -= roemzij;
		totaalpuntenwij -= puntenwij;
		totaalpuntenzij -= puntenzij;

		totaalwij = totaalpuntenwij + totaalroemwij;
		totaalzij = totaalpuntenzij + totaalroemzij;


		$('#scorelist tr').last().remove();
		$('#scorelist tr').last().remove();
		if (round > 1) {
			showSubTotals();
		}


		resetRound();
		updateGameState();

		// TODO: resultaten van gewiste ronde invoerklaar hebben
	}
} // undoLastMove

// set score for wij nat
function wijnat() {
	roemzij = roemzij + roemwij;
	roemwij = 0;
	puntenwij = 0;
	puntenzij = puntentotaal;
	wijstatus = "N";
}

// set score for zijnat
function zijnat() {
	roemwij = roemwij + roemzij;
	roemzij = 0;
	puntenzij = 0;
	puntenwij = puntentotaal;
	zijstatus = "N";
}

// set score for wijverzaken
function wijverzaken() {
	roemzij = roemzij + roemwij + 100;
	roemwij = 0;
	puntenwij = 0;
	puntenzij = puntentotaal;
	wijstatus = "V";
	zijstatus = "";
}

// set score for zijverzaken
function zijverzaken() {
	roemwij = roemwij + roemzij + 100;
	roemzij = 0;
	puntenzij = 0;
	puntenwij = puntentotaal;
	zijstatus = "V";
	wijstatus = "";
}

// set score for wijpit
function wijpit() {
	roemwij += 100;
	puntenwij = puntentotaal;
	puntenzij = 0;
	wijstatus = "P";
}

// set score for zijpit
function zijpit() {
	roemzij += 100;
	puntenzij = puntentotaal;
	puntenwij = 0;
	zijstatus = "P";
}



// process the input from the current round on the screen and in the scorelist
function processRound(logit, confirmed) {

	// check input
	if(puntenwij + puntenzij != puntentotaal || puntenwij == 1 || puntenzij == 1) {
		errorAlert("Ongeldig puntentotaal");
		return 1;
	}
	if(wie == "" || op == "" || voor == 0) {
		errorAlert("Missende gegevens");
		return 1;
	}

	// check if this round would end the game, ask for confirmation
	if(logit && !confirmed) {
		var nieuwtotaalwij = totaalwij + roemwij + puntenwij;
		var nieuwtotaalzij = totaalzij + roemzij + puntenzij;
		if(nieuwtotaalwij >= 1500 || nieuwtotaalzij >= 1500) {
			$('#bevestigModalScore').html(
				'Score na deze hand &mdash; ' +
				'<strong>Wij:</strong> ' + nieuwtotaalwij +
				' &nbsp; <strong>Zij:</strong> ' + nieuwtotaalzij
			);
			$('#bevestigJa').off('click').on('click', function() {
				processRound(logit, true);
			});
			$('#bevestigModal').modal('show');
			return 1;
		}
	}

	var wijveld = "";
	var zijveld = "";

	if(voor > puntenwij && wijstatus != "V" && zijstatus != "V" && team == "wij") {
			errorAlert("Wij zijn nat");
			roemzij = roemzij + roemwij;
			roemwij = 0;
			puntenwij = 0;
			puntenzij = puntentotaal;
			wijstatus = "N";
	}

	if(voor > puntenzij && wijstatus != "V" && zijstatus != "V" && team == "zij") {
			errorAlert("Zij zijn nat");

			roemwij = roemwij + roemzij;
			roemzij = 0;
			puntenzij = 0;
			puntenwij = puntentotaal;
			zijstatus = "N";

	}

	if(zijstatus == 0 && wijstatus == 0 && voor <= puntenwij && team == "wij") {
	wijstatus = "G";
	}

	if(wijstatus == 0 && zijstatus == 0 && voor <= puntenzij && team == "zij") {
	zijstatus = "G";
	}




	// wij status
	if(wijstatus == "N") {
		wijveld = "<span class=\"text-danger\">N</span>";
	}
	else if(wijstatus == "P") {
		wijveld = "<span class=\"text-success\">P</span>";
	}
	else if(wijstatus == "V") {
		wijveld = "<span class=\"text-warning\">V</span>";
	}
	else if(wijstatus == "G") {
		wijveld = "<span class=\"text-primary\">G</span>";
	}
	else {
		wijveld = "";
	}

	// zij status
	if(zijstatus == "N") {
		zijveld = "<span class=\"text-danger\">N</span>";
	}
	else if(zijstatus == "P") {
		zijveld = "<span class=\"text-success\">P</span>";
	}
	else if(zijstatus == "V") {
		zijveld = "<span class=\"text-warning\">V</span>";
	}
	else if(zijstatus == "G") {
		zijveld = "<span class=\"text-primary\">G</span>";
	}
	else {
		zijveld = "";
	}


	// store game status
	//spel[0] = spelers[0]
	//spel[1] = spelers[1]
	//spel[2] = spelers[2]
	//spel[3] = spelers[3]
	gamestats[round-1][0] = puntenwij; //check
	gamestats[round-1][1] = roemwij; //check
	gamestats[round-1][2] = wijstatus;
	gamestats[round-1][3] = puntenzij; //check
	gamestats[round-1][4] = roemzij; //check
	gamestats[round-1][5] = zijstatus;
	gamestats[round-1][6] = wie; //check
	gamestats[round-1][7] = op; //check
	gamestats[round-1][8] = voor; //check
	gamestats[round-1][9] = puntentotaal;
	gamestats[round-1][10] = team; //check



	// store result row
	$.cookie('boernel_cookie', gamestats, { expires: 365 });
	$.cookie('boernel_dateId', boernelDateId, { expires: 365 });
	$.cookie('boernel_spel', spelers, { expires: 365 });


	// update totals
	totaalroemwij += roemwij;
	totaalroemzij += roemzij;
	totaalpuntenwij += puntenwij;
	totaalpuntenzij += puntenzij;
	totaalwij = totaalpuntenwij + totaalroemwij;
	totaalzij = totaalpuntenzij + totaalroemzij;

	// log result row
	//if(logit)
		//$.post("config/get.php", { 'Speler1': spelers[0], 'Speler2': spelers[1], 'Speler3': spelers[2], 'Speler4': spelers[3]});



	// log result row
	if(logit)
		var speler1data = 1200;
		var speler2data = 1200;
		var speler3data = 1200;
		var speler4data = 1200;
		$.post("config/put.php", { 'wie': wie, 'round': round, 'dateId': boernelDateId, 'op': op, 'voor': voor, 'PuntenTotaalWij': totaalwij, 'PuntenTotaalZij': totaalzij, 'Team': team, 'Speler1': spelers[0], 'Speler2': spelers[1], 'Speler3': spelers[2], 'Speler4': spelers[3], 'PuntenWij': puntenwij, 'PuntenZij': puntenzij, 'RoemWij': roemwij,'RoemZij': roemzij, 'deler': deler, 'Verzaker': verzaker, 'StatusWij': wijstatus, 'StatusZij': zijstatus});



	// add row of current round to scorelist
	if(round > 1) {
		$('#scorelist tr').last().remove();
	}
	$("#scorelist").append("<tr id=\"round" + round + "\" style=\"display: none;\"><td class=\"text-right\">"
	+ roemwij + "</td><td class=\"text-right\">"
	+ wijveld + " " + puntenwij + "</td><td class=\"text-center text-strong\">"
	+ round + ". " + wie + " (" + voor + ")</td><td class=\"text-left\">"
	+ puntenzij + " " + zijveld + "</td><td class=\"text-left\">"
	+ roemzij + "</td></tr>");
	$("#round" + round).fadeIn();

	// reset current round state
	resetRound();

	// reset input fields
	resetInputFields();

	// update current round number
	if(totaalwij < 1500 || totaalzij < 1500)
		successAlert("Hand " + round + " verwerkt");
	else {
		if(totaalwij > totaalzij) {
			successAlert("Wij winnen");
		}
		else if(totaalzij > totaalwij) {
			successAlert("Zij winnen");
		}
		else {
			successAlert("Gelijkspel");
		}
	}
	round++;
	deler = spelers[VOLGORDE[(round - 1 + VOLGORDE[deler1 - 1] - 1) % 4] - 1];
	spelers[4] = deler1;

	// show row with final totals
	if(totaalwij > 1499 || totaalzij > 1499) {
		disableAllButtons();
		showTotals();
		$("#resetbutton").fadeIn();
	}

	// show row with subtotals
	else if(round > 1) {
		showSubTotals();
	}

	// update state of game
	updateGameState();

} // processRound

// delete the (only) boernel cookie
function deleteCookie() {
	$.removeCookie("boernel_cookie");
	$.removeCookie("boernel_dateId");
	$.removeCookie("boernel_spel");
} // deleteCookie



function restoreFromList(items,spelitems) {

	spelers[0] = spelitems[0];
	spelers[1] = spelitems[1];
	spelers[2] = spelitems[2];
	spelers[3] = spelitems[3];
	deler1 = parseInt(spelitems[4]);

	$("#speler1").text(spelers[0]);
	$("#speler1V").text(spelers[0]);
	$("#speler1D").text(spelers[0]);
	if(spelers[0] != "Speler 1") {
	$('#Teller').removeClass('btn-default').addClass('btn-success');
	}
	$("#speler2").text(spelers[1]);
	$("#speler2V").text(spelers[1]);
	$("#speler2D").text(spelers[1]);
			if(spelers[1] != "Speler 2") {
	$('#Maat').removeClass('btn-default').addClass('btn-success');
	}
	$("#speler3").text(spelers[2]);
	$("#speler3V").text(spelers[2]);
	$("#speler3D").text(spelers[2]);
			if(spelers[2] != "Speler 3") {
	$('#West').removeClass('btn-default').addClass('btn-success');
	}
	$("#speler4").text(spelers[3]);
	$("#speler4V").text(spelers[3]);
	$("#speler3D").text(spelers[3]);
			if(spelers[3] != "Speler 4") {
	$('#Oost').removeClass('btn-default').addClass('btn-success');
	}

	offset = 0;


	while(items[offset] || items[offset+1] || items[offset+2] || items[offset+3] || items[offset+4] || items[offset+5] || items[offset+6] || items[offset+7] || items[offset+8] || items[offset+9] || items[offset+10]) {
		// fetch results of last round
		puntenwij = parseInt(items[offset+0]);
		roemwij = parseInt(items[offset+1]);
		wijstatus = items[offset+2];
		puntenzij = parseInt(items[offset+3]);
		roemzij = parseInt(items[offset+4]);
		zijstatus = items[offset+5];
		wie = items[offset+6];
		op = items[offset+7];
		voor = parseInt(items[offset+8]);
		puntentotaal = parseInt(items[offset+9]);
		team = items[offset+10];

		if(items[offset+2] == "P") {
			roemwij -= 100;
			wijpit();
		}
		else if(items[offset+2] == "N") {
			wijnat();
		}
		else if(items[offset+2] == "V") {
			roemzij -= 100;
			wijverzaken();
		}
		else if(items[offset+5] == "P") {
			roemzij -= 100;
			zijpit();
		}
		else if(items[offset+5] == "N") {
			zijnat();
		}
		else if(items[offset+5] == "V") {
			roemwij -= 100;
			zijverzaken();
		}
		processRound(false);
		offset += 11;
	}
} // restoreFromList

// restore the game state from the cookie
function restoreFromCookie() {
	// check if we must process the gamestats from a cookie
	if(round == 1 && $.cookie('boernel_cookie')) {
		boernelDateId = parseInt($.cookie('boernel_dateId'));
		// check if it is an array
		var items = $.cookie('boernel_cookie') ? $.cookie('boernel_cookie').split(/,/) : new Array();
		var spelitems = $.cookie('boernel_spel') ? $.cookie('boernel_spel').split(/,/) : new Array();
		restoreFromList(items,spelitems);
		return true;
	}
	return false;
} // restoreFromCookie

function setButtonFunctionality() {

	// select on text on clicking input field
	$(".selectonclick").click(function(){
		this.focus();
		this.select();
	});

	// roem wij buttons
	$("#roemwijbuttons").children("button").click(function() {
		temp = 0;
		if(parseInt(this.value) == -1) {
			if(roemwij_list.length > 0) {
				temp = -1 * roemwij_list[roemwij_list.length-1];
			}
			roemwij_list.pop();
		}
		else {
			temp = parseInt(this.value);
			roemwij_list.push(parseInt(this.value));
		}
		roemwij += temp;
		if(roemwij > 0 && team == "zij") {
			$("#pit").removeAttr("disabled");
		}
		else {
			$("#pit").removeAttr("disabled");
		}
		$("#roemwij").val(roemwij);
	});



// op welke kleur buttoms
	$("#opwelkekleur").children("button").click(function() {
	op = kleuren[parseInt(this.value) - 1];
	$('#opwelkekleur').children("button").removeClass('btn-primary').addClass('btn-default ');
	$(this).removeClass('btn-default').addClass('btn-primary ');
	$('#voorhoeveel').children("button").removeClass('btn-primary').addClass('btn-default ');
	voor = 0;
	$('#voorhoeveel').children("button").removeAttr("disabled");
	if(op == "Sans") {
	puntentotaal = 130;
	$("#140").attr("disabled", "disabled");
	$("#150").attr("disabled", "disabled");
	$("#162").attr("disabled", "disabled");
	}
	else {
	puntentotaal = 162;
	$("#70").attr("disabled", "disabled");
	}
	$('#opwelkekleur').children("button").removeClass('btn-primary').addClass('btn-default ');
	$(this).removeClass('btn-default').addClass('btn-primary ');

	});


// voor hoeveeel buttoms
	$("#voorhoeveel").children("button").click(function() {
	voor = parseInt(this.value);
	$('#voorhoeveel').children("button").removeClass('btn-primary').addClass('btn-default ');
	$(this).removeClass('btn-default').addClass('btn-primary ');

	});

	//delerbutton
	$("#eerstedeler").children("button").click(function() {
	deler1 = parseInt(this.value);
	$('#eerstedeler').children("button").removeClass('btn-primary').addClass('btn-default');
	$(this).removeClass('btn-default').addClass('btn-primary');
	if(spelers[0] != "Speler 1" && spelers[1] != "Speler 2" && spelers[2] != "Speler 3" && spelers[3] != "Speler 4" && deler1 != 0)
	{
	$("#klaar").removeAttr("disabled");
	deler = spelers[VOLGORDE[(round - 1 + VOLGORDE[deler1 - 1] - 1) % 4] - 1];
	spelers[4] = deler1;
	$("#dealer").html(deler);

	}
	});//delerbutton

	// roem zij buttoms
	$("#roemzijbuttons").children("button").click(function() {
		temp = 0;
		if(parseInt(this.value) == -1) {
			if(roemzij_list.length > 0) {
				temp = -1 * roemzij_list[roemzij_list.length-1];
			}
			roemzij_list.pop();
		}
		else {
			temp = parseInt(this.value);
			roemzij_list.push(parseInt(this.value));
		}
		roemzij += temp;
		if(roemzij > 0 && team == "wij") {
			$("#pit").removeAttr("disabled");
		}
		else {
			$("#pit").removeAttr("disabled");
		}
		$("#roemzij").val(roemzij);
	});

	// punten wij invoer
	$("#puntenwij").keyup(function() {
		puntenwij = parseInt(this.value);
		temp = puntentotaal - puntenwij;
		if(temp >= 0) {
			puntenzij = temp;
			$("#puntenzij").val(puntenzij);
		}
	});

	// punten zij invoer
	$("#puntenzij").keyup(function() {
		puntenzij = parseInt(this.value);
		temp = puntentotaal - puntenzij;
		if(temp >= 0) {
			puntenwij = temp;
			$("#puntenwij").val(puntenwij);
		}
	});

	// verwerk-knop
	$("#verwerk").click(function() {
		processRound(true);
	});

	// reset-knop
	$("#resetbutton").click(function() {
		resetGame();
		deleteCookie();
		resetPlayerButtons();
		checkspelers();
	});

	// Knop speler1
	$("#speler1").click(function() {
	wie = spelers[0];
	team = "wij";
  	$('#speler1').removeClass('btn-default').addClass('btn-primary ');
  	$('#speler2').removeClass('btn-primary').addClass('btn-default ');
	$('#speler3').removeClass('btn-primary').addClass('btn-default ');
	$('#speler4').removeClass('btn-primary').addClass('btn-default ');
	});

	// Knop speler2
	$("#speler2").click(function() {
  	wie = spelers[1];
  	team = "wij";
  	$('#speler2').removeClass('btn-default').addClass('btn-primary ');
  	$('#speler1').removeClass('btn-primary').addClass('btn-default ');
	$('#speler3').removeClass('btn-primary').addClass('btn-default ');
	$('#speler4').removeClass('btn-primary').addClass('btn-default ');
	});

	// Knop speler3
	$("#speler3").click(function() {
	wie = spelers[2];
	team = "zij";
  	$('#speler3').removeClass('btn-default').addClass('btn-primary ');
  	$('#speler2').removeClass('btn-primary').addClass('btn-default ');
	$('#speler1').removeClass('btn-primary').addClass('btn-default ');
	$('#speler4').removeClass('btn-primary').addClass('btn-default ');
	});

	// Knop speler4
	$("#speler4").click(function() {
	wie = spelers[3];
	team = "zij";
  	$('#speler4').removeClass('btn-default').addClass('btn-primary ');
  	$('#speler2').removeClass('btn-primary').addClass('btn-default ');
	$('#speler3').removeClass('btn-primary').addClass('btn-default ');
	$('#speler1').removeClass('btn-primary').addClass('btn-default ');
	});

	// reset-knop
	$("#resetnow").click(function() {
		resetGame();
		deleteCookie();
		resetPlayerButtons();
		checkspelers();
	});

	// nat-knop
	$("#nat").click(function() {
		if(wie == "" || op == "" || voor == 0) {
		errorAlert("Missende gegevens");
		return 1;
	}
		if(wie == spelers[0] || wie == spelers[1]) {
		wijnat();
		processRound(true);
		}
		else if(wie == spelers[2] || wie == spelers[3]) {
		zijnat();
		processRound(true);
		}
	});

	// boeren-knop
	$("#boeren").click(function() {
	wil = 1 + Math.floor(Math.random() * 3);
	spelersoud[0] = spelers[0];
	spelersoud[1] = spelers[1];
	spelersoud[2] = spelers[2];
	spelersoud[3] = spelers[3];

	spelers[1] = spelersoud[wil];
	if(wil == 2) {
		spelers[2] = spelersoud[1];
	}
	if(wil == 3) {
		spelers[3] = spelersoud[1];
	}
	$("#speler1").text(spelers[0]);
	$("#speler1V").text(spelers[0]);
	$("#speler1D").text(spelers[0]);
	$("#Teller").text(spelers[0]);
	$("#speler2").text(spelers[1]);
	$("#speler2V").text(spelers[1]);
	$("#speler2D").text(spelers[1]);
	$("#Maat").text(spelers[1]);
	$("#speler3").text(spelers[2]);
	$("#speler3V").text(spelers[2]);
	$("#speler3D").text(spelers[2]);
	$("#West").text(spelers[2]);
	$("#speler4").text(spelers[3]);
	$("#speler4V").text(spelers[3]);
	$("#speler4D").text(spelers[3]);
	$("#Oost").text(spelers[3]);
	$("#boeren").attr("disabled", "disabled");
	myAlert2("Er is geboerd!");

	});

	// op welke kleur buttoms
	$("#verzaker").children("button").click(function() {
	verzaker = spelers[parseInt(this.value) - 1];
	$('#myModal').modal('hide');
	if(verzaker == spelers[0] || verzaker == spelers[1]){
	wijverzaken();
	processRound(true);
	}
	if(verzaker == spelers[2] || verzaker == spelers[3]){
	zijverzaken();
	processRound(true);
	}
	});



	// pit-knop
	$("#pit").click(function() {
				if(wie == "" || op == "" || voor == 0) {
		errorAlert("Missende gegevens");
		return 1;
		}
			if(wie == spelers[0] || wie == spelers[1]) {
		wijpit();
		processRound(true);
		}
		else if(wie == spelers[2] || wie == spelers[3]) {
		zijpit();
		processRound(true);
		}
	});



	// undo-move button
	$("#undolastmove").click(function() {
		if(round > 1) {
			undoLastMove();
			successAlert("Vorige hand ongedaan gemaakt");
		}
	});

}

$(document).ready(function() {

	// make the buttons work
	setButtonFunctionality();

	// reset game upon initialization
	resetGame();
	resetRound();
	resetInputFields();

	// check if we must restore something from the cookie
	if(restoreFromCookie()) {
		successAlert("Vorige sessie hersteld!");
	} // if
	// otherwise prepare a new game
	else {


		updateGameState();
		checkspelers();
		successAlert("Nieuw spel gereed!");
	} // else

}); // document.ready
