<?php
namespace Core;
require_once('../mongo.php');

use \Lib\Mongo as Mongo;

Mongo::setDBName('100k1');

$gameId = intval($_GET['gameId']);

$game = Mongo::fetchOne('games', array('_id' => $gameId));
if(!$game) {
    jsonError(1, 'Game not exists');
}

// Get current game state
if(empty($_GET['action'])) {
    jsonResponse($game);
} else {
    switch($_GET['action']) {
        case 'openAnswer':
            $questionIndex = $game['currentQuestion'];
            $answerIndex = intval($_GET['answerIndex']);
            $team = intval($_GET['team']);

            $game['questions'][$questionIndex]['answers'][$answerIndex]['opened'] = $team;
            updateGame($gameId, array('$set' => array(
                'questions' => $game['questions'],
            )));

            if($team >= 0) {
                updateGame($gameId, array('$inc' => array(
                    'score' => $game['questions'][$questionIndex]['answers'][$answerIndex]['points']
                )));
            }
            break;
        case 'endQuestion':
            $team = intval($_GET['team']);

            $game['teams'][$team]['score'] += $game['score'];
            if($team > 0) {
                updateGame($gameId, array('$set' => array(
                    'teams' => $game['teams'],
                    'score' => 0
                )));
            }
            break;
        case 'nextQuestion':
            if($game['score']) {
                jsonError(2, 'Score is not empty, you must choose winner first');
            }


            $game['teams'][0]['errors'] = 0;
            $game['teams'][1]['errors'] = 0;
            updateGame($gameId, array('$inc' => array(
                'currentQuestion' => 1
            ), '$set' => array(
                'teams' => $game['teams']
            )));
            break;
        case 'teamError':
            $team = intval($_GET['team']);

            $game['teams'][$team]['errors']++;
            updateGame($gameId, array('$set' => array(
                'teams' => $game['teams']
            )));

            break;
    }

    $error = Mongo::getLastError();
    if($error) {
        jsonError(0, $error);
    } else {
        jsonResponse(true);
    }
}

function jsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    die();
}

function jsonError($errorCode = 0, $errorMessage = '') {
    jsonResponse(array(
        'error' => array(
            'code' => $errorCode,
            'message' => $errorMessage
        )
    ));
}

function updateGame($gameId, $update = array()) {
    return Mongo::update('games', array(
        'game_id' => $gameId
    ), $update);
}