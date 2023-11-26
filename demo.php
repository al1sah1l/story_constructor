<?php
include_once 'allConnections.php';

/** @var Story[] $lib*/
$lib = Library::getStories();
$id = getStoryId();
/** @var Story $story*/
$story = Library::findStoryByID($lib, $id);

$id = $_GET['id'];

if ($_GET['mode']){
    if ($_GET['mode'] == 'delete'){
        $story->delete();
        header('Location: home.php');
    } elseif ($_GET['mode'] == 'del'){
        $story->deleteEpisode($id, true);
        $story->saveToFile();
        $url = 'demo.php?id=' . $story->episodesList[0]->id;
        header('Location: ' . $url);
    } else {
        $story->addEpisode();
        $story->saveToFile();

        $choice_num = getChoiceId() - 1;
        $lastEpisode = count($story->episodesList) - 1;
        $lastEpisodeId = $story->episodesList[$lastEpisode]->id;

        $story->findEpisode($id)->options[$choice_num]->nextItemId = $lastEpisodeId;
        $story->saveToFile();
        $new_url = 'demo.php?id=' . $story->episodesList[$lastEpisode++]->id;
        header('Location: '.$new_url);
    }
}

if ($_POST['story_name']){
    addNewStory($_POST['story_name']);
}

$id = $_GET['id'];
$currentEpisode = $story->findEpisode($id);

if($_POST['desc'] || $_POST['name'] || $_POST['choice_name']){
    $story->renovateEpisode($currentEpisode->renovateInfo());
}



echo generateDemo($story, $currentEpisode);
?>
