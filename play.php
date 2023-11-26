<?php
include_once 'allConnections.php';

/** @var Story[] $lib*/
$lib = Library::getStories();
$id = getStoryId();
/** @var Story $story*/
$story = Library::findStoryByID($lib, $id);
$firstEp = $story->sortEpisodes()[0]->id;
$id = $_GET['id'];

$exactEpisode = $story->findEpisode($id);
echo playEpisode($story->name, $exactEpisode, $firstEp);

