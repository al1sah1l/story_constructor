<?php
function addNewStory($name)
{
    $story = new Story($name);
    $story->saveToFile();
    $url = 'demo.php?id=' . $story->episodesList[0]->id;;
    header('Location: '.$url);
}

function getStoryId()
{
    $story = explode('_', $_GET['id']);
    return $story[0];
}

function getChoiceId()
{
    if ($_POST){
        $story = explode('_', $_POST['choice_id']);
        return $story[2] - 1;
    } else {
        $story = explode('_', $_GET['mode']);
        return $story[1];
    }
}

/**
 * @param string $href - the link for our block
 * @param string $content - content information that will be used inside of a link
 * @return string - result
 */
function addLink($href, $content)
{
    return
        '<a href="'.$href.'">'.
        $content.
        '</a>';
}

/**
 * @param Story[] $library
 * @return string
 */
function getStoriesList($library)
{
    $cont = '';
    $i = 0;
    /** @var Story $story*/
    foreach ($library as $story) {
        $href = 'demo.php?id='.$story->shortCode.'_0';
        $content =
            '<div class="list_item">' .
            $story->name .
            '</div>';
        $cont .= addLink($href, $content);
        $i++;
    }
    $href = '#story';
    $content = '<div class="list_item delete">
        Add new Story
        </div>';
    $cont .= addLink($href, $content);
    return '<div class="library_list">' . $cont . '</div>';
}

function drawScheme($episode)
{
    $point = new Point(220, 200);
    $drawer = new Drawer($point);
    $content = $drawer->drawWholeEpisode($episode, $point);
    $scheme = $drawer->drawSVG($content);

    return
        '<head>
            <meta charset="utf-8">
            <title>Demo</title>
            <link rel="stylesheet" href="style.css">
        </head>
        <body>
            <div class="top_bar">
                <div style="padding: 7px 10px;">
                <a href="home.php">
                    <p>
                        Back to Library  
                    </p>
                </a>
                <a href="play.php?id='.$episode->id.'">
                    <p>
                        Play the Story
                    </p>
                </a>
                <a href="demo.php?id='.$episode->id.'&mode=delete">
                    <p>
                        Delete the Story
                    </p>
                </a>    
            </div>
            <div style="width: 1200px">
                <h3>Online-constructor of the exact episode:</h3>
            </div>' . $scheme .
        '</body>';
}

/**
 * @param Story $story
 * @param Episode $episode
 * @return string
 */
function generateDemo($story, $episode)
{
    ob_end_clean();
    $content = drawScheme($episode);
    $content .= getEpisodesList($story);
    $content .= callEpisodeModal($episode);
    $i = 1;
    foreach ($episode->options as $option)
    {
        $content .= callChoiceModal($episode, $i++);
    }

    return $content;
}

/**
 * @param Story $story - the story to print the list of its episodes
 * @return string
 */
function getEpisodesList($story)
{
    $cont = '<h3 style="margin-bottom: 10px">List of Episodes:</h3>';
    foreach ($story->sortEpisodes() as $episode) {
        $href = '?id=' . $episode->id;
        $content =
            '<div class="list_block">' .
            $episode->name .
            '</div>';
        $cont .= addLink($href, $content);
        $content = '
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 30 30" width="30px" height="30px">
<g id="surface1141216">
<path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 0 15 C 0 6.714844 6.714844 0 15 0 C 23.285156 0 30 6.714844 30 15 C 30 23.285156 23.285156 30 15 30 C 6.714844 30 0 23.285156 0 15 Z M 15 28.800781 C 22.621094 28.800781 28.800781 22.621094 28.800781 15 C 28.800781 7.378906 22.621094 1.199219 15 1.199219 C 7.378906 1.199219 1.199219 7.378906 1.199219 15 C 1.199219 22.621094 7.378906 28.800781 15 28.800781 Z M 15 28.800781 "/>
<path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 13.375 6.875 L 12.5625 7.6875 L 7.6875 7.6875 L 7.6875 9.3125 L 8.589844 9.3125 L 10.039062 21.707031 L 10.039062 21.714844 C 10.144531 22.515625 10.839844 23.125 11.648438 23.125 L 18.351562 23.125 C 19.15625 23.125 19.855469 22.515625 19.960938 21.714844 L 19.960938 21.707031 L 21.410156 9.3125 L 22.3125 9.3125 L 22.3125 7.6875 L 17.4375 7.6875 L 16.625 6.875 Z M 10.226562 9.3125 L 19.773438 9.3125 L 18.351562 21.5 L 11.648438 21.5 Z M 10.226562 9.3125 "/>
</g>
</svg>';
        $cont .= addLink($href . '&mode=del', $content);
    }
    return '<div class="list">' . $cont . '</div>';
}

/**
 * @param Episode $episode
 * @return string
 */
function callEpisodeModal($episode){
    $link = 'demo.php?id=' . $episode->id;
    return
    '<a href="#" class="overlay" id="episode"></a>
    <div class="popup episode_popup">
        <form name="episode" id="'.$episode->id.'" method="post" action="' .$link. '" >
            <div style="text-align: center;">
                <label for="episode_name" style="padding-right: 36px">Name:</label>
                <input type="text" required style="width: 550px" name="name" value="'.$episode->name.'">
                <br>
                <br>
                <label for="episode_id" style="padding-right: 3px">Episode ID:</label>
                <input type="text" disabled style="width: 550px" name="id" value="'.$episode->id.'">
                <br>
                <br>
                <label for="episode_desc" style="vertical-align: top">Description:</label>
                <textarea rows="8" cols="50" name="desc">'.$episode->description.'</textarea>
            </div>
            <br>
            <div class="buttons_block">
                <a href="#close"><input type="button" value="Cancel" class="cancelBut"></a>
                <input type="submit" value="Save" class="saveBut">
            </div>
        </form>
    </div>';
}

/**
 * @param Episode $episode
 * @param int $choiceID - 1, 2 or 3
 * @return string
 */
function callChoiceModal($episode, $choiceID){
    $link = 'demo.php?id=' . $episode->id;
    $wholeID = $episode->id.'_'.$choiceID;
    /** @var ChoiceOption $currentChoice*/
    $currentChoice = $episode->options[$choiceID-1];
    return
        '<a href="#" class="overlay" id="choice'.$choiceID.'"></a>
    <div class="popup choice_popup">
        <form name="choice" id="'.$wholeID.'" method="post" action="' .$link. '" >
            <div style="text-align: center;">
                <label for="choice_name" style="padding-right: 61px">Name:</label>
                <input type="text" style="width: 525px" name="choice_name" value="'.$currentChoice->optionName.'">
                <br>
                <br>
                <label for="choice_id" style="padding-right: 34px">Choice ID:</label>
                <input type="text" readonly="" style="width: 525px" name="choice_id" value="'.$wholeID.'">
                <br>
                <br>
                <label for="choice_link" style="padding-right: 3px">Linked episode:</label>
                <input type="text" style="width: 525px" name="choice_link" value="'.$currentChoice->nextItemId.'">
                <br>
                <br>
            </div>
            <br>
            <div class="buttons_block">
                <a href="#close"><input type="button" value="Cancel" class="cancelBut"></a>
                <input type="submit" value="Save" class="saveBut">
            </div>
        </form>
    </div>';
}

function callAddModal()
{
    $link = 'demo.php';
    return
        '<a href="#" class="overlay" id="story"></a>
    <div class="popup">
        <form name="story" id="story_id" method="post" action="' .$link. '" >
            <div style="text-align: center;">
                <label for="story_name" style="padding-right: 61px">Name:</label>
                <input type="text" style="width: 525px" required name="story_name">
            </div>
            <br>
            <div class="buttons_block">
                <a href="#close"><input type="button" value="Cancel" class="cancelBut"></a>
                <input type="submit" value="Save" class="saveBut">
            </div>
        </form>
    </div>';
}

/**
 * @param $storyName
 * @param Episode $episode
 * @param string $firstEpId
 * @return string
 */
function playEpisode($storyName, $episode, $firstEpId)
{
    ob_end_clean();
    $options = '';
    $status = 0;
    foreach ($episode->options as $option){
        if ($option->nextItemId != null){
            $link = 'play.php?id='.$option->nextItemId;
            $cont = '
            <div>'.
              $option->optionName
            .'</div>';
            $options .= addLink($link, $cont);
            $status++;
        }
    }
    if ($status == 0){
        $link = 'play.php?id=' . $firstEpId;
        $cont = '
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 192 192" width="192px" height="192px">
        <g id="surface1654831">
        <path style=" stroke:none;fill-rule:nonzero;fill:rgb(11.764706%,56.470591%,100%);fill-opacity:1;" d="M 0 96 C 0 42.980469 42.980469 0 96 0 C 149.019531 0 192 42.980469 192 96 C 192 149.019531 149.019531 192 96 192 C 42.980469 192 0 149.019531 0 96 Z M 96 184.320312 C 144.777344 184.320312 184.320312 144.777344 184.320312 96 C 184.320312 47.222656 144.777344 7.679688 96 7.679688 C 47.222656 7.679688 7.679688 47.222656 7.679688 96 C 7.679688 144.777344 47.222656 184.320312 96 184.320312 Z M 96 184.320312 "/>
        <path style=" stroke:none;fill-rule:nonzero;fill:rgb(11.764706%,56.470591%,100%);fill-opacity:1;" d="M 51.800781 44 C 51.28125 44 50.238281 43.996094 49.71875 44.519531 C 49.199219 45.558594 49.199219 47.640625 49.71875 48.683594 L 59.753906 58.714844 C 50.003906 68.285156 44 81.527344 44 96 C 44 124.773438 67.226562 148 96 148 C 124.773438 148 148 124.773438 148 96 C 148 69.441406 127.757812 47.433594 101.777344 44.546875 C 101.605469 44.53125 101.433594 44.519531 101.261719 44.519531 C 98.484375 44.480469 96.167969 46.628906 95.996094 49.398438 C 95.828125 52.167969 97.863281 54.585938 100.621094 54.886719 C 121.441406 57.199219 137.601562 74.71875 137.601562 96 C 137.601562 119.226562 119.226562 137.601562 96 137.601562 C 72.773438 137.601562 54.398438 119.226562 54.398438 96 C 54.398438 84.335938 59.238281 73.707031 67.085938 66.050781 L 75.71875 74.683594 C 76.757812 75.203125 78.84375 75.203125 79.882812 74.683594 C 80.402344 74.160156 80.398438 73.121094 80.398438 72.601562 L 80.398438 49.199219 C 80.398438 46.078125 78.320312 44 75.199219 44 Z M 51.800781 44 "/>
        </g>
        </svg>';
        $options = addLink($link, $cont);
    }

    $content =
        '<div class="play_page">
            <div>
                <h2>'.$storyName.'</h2>
            </div>
            <div class="story_desc">'.$episode->description.'</div>
            <div class="play_options">'
                .$options.
            '</div>
        </div>';

    return '<head>
            <meta charset="utf-8">
            <title>Demo</title>
            <link rel="stylesheet" href="style.css">
        </head>
        <body style="background-color: azure">
            <div class="top_bar">
                <div style="padding: 7px 10px;">
                <a href="home.php">
                    <p>
                        Back to Library  
                    </p>
                </a>
                <a href="demo.php?id='.$episode->id.'">
                    <p>
                        Edit the Story
                    </p>
                </a>
                <a href="demo.php?id='.$episode->id.'&mode=delete">
                    <p>
                        Delete the Story
                    </p>
                </a>    
            </div>
            ' . $content .
        '</body>';
}
