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

$questionIndex = $game['currentQuestion'];

// Get current game state
if(empty($_GET['action'])) {
    jsonResponse($game);
} else {
    switch($_GET['action']) {
        case 'openAnswer':
            $answerIndex = intval($_GET['answerIndex']);
            $team = intval($_GET['team']);
            updateGame($gameId, array('$set' => array(
                'questions.' . $questionIndex . '.answers.' . $answerIndex . '.opened' => true,
            )));

            if($team >= 0) {
                updateGame($gameId, array('$inc' => array(
                    'score' => $game['questions'][$questionIndex]['answers'][$answerIndex]['points'] * ($questionIndex <= 2 ? $questionIndex + 1 : 1)
                )));
            }
            break;
        case 'endQuestion':
            $team = intval($_GET['team']);

            if($team > 0) {
                updateGame($gameId, array('$inc' => array(
                    'teams.' . $team . '.score' => $game['score'],
                ), '$set' => array(
                    'score' => 0
                )));
            }
            break;
        case 'nextQuestion':
            if($game['score']) {
                jsonError(2, 'Score is not empty, you must choose winner first');
            }

            if($questionIndex < count($game['questions']) - 1) {
                updateGame($gameId, array('$inc' => array(
                    'currentQuestion' => 1
                ), '$set' => array(
                    'teams[0].errors' => 0,
                    'teams[1].errors' => 0
                )));
            } else {
                jsonError(0, 'No more questions');
            }
            break;
        case 'teamError':
            $team = intval($_GET['team']);

            updateGame($gameId, array('$inc' => array(
                'teams.' . $team . '.errors' => 1
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
        '_id' => $gameId
    ), $update);
}