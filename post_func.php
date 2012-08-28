<?php
function post_get($db, $xtopic, $xbegin)
{
  if (is_numeric($xtopic) && (is_numeric($xbegin)))
  {
    $limit = 20;
    $query = $db->prepare("SELECT topic_title, topic_text, topic_replies, topic_by, user_name, permissions, sticky, locked FROM topics LEFT JOIN users ON topic_by = user_id WHERE topic_id = ?");
    $query->execute(array($xtopic));
    if ($query->rowCount() < 1) {
      echo '<div id="topic_title" name=0 topic_replies=0 begin=0 end=0 limit=0>Unknown Topic</div>';
      die();
    }
    
    $topic = $query->fetch(PDO::FETCH_ASSOC);

    if ($xbegin == 0) {
      $begin = $topic['topic_replies'] - $limit;
    } else {
      $begin = $xbegin - 1;
    }
    if ($begin > $topic['topic_replies'] - 1) {
      $begin = $topic['topic_replies'] - 1;
    }
    if ($begin < 0) {
      $begin = 0;
    }

    $query = $db->prepare(" SELECT p.post_text, p.post_id, p.post_by, u.user_name, u.user_id, u.permissions FROM posts AS p INNER JOIN users AS u ON p.post_by = u.user_id WHERE post_topic = ? ORDER BY post_date ASC LIMIT $begin, $limit");
    $query->execute(array($xtopic));
    
    $end = $query->rowCount() + $begin - 1;
    
    echo '<div id="topic_title" name='.$xtopic.' topic_replies='.$topic['topic_replies']
      .' begin='.($begin+1).' end='.($end+1).' limit='.$limit.'>';displaytopiclocked($topic['topic_title'],$topic['locked']); echo'</div>';
    echo '<div id="topic_text">' . "<span class='username' style='font-weight: bold;'>"; theusername($topic['user_name'],$topic['permissions']); echo ":</span> " . $topic['topic_text'].'<div class="postbuttons">'; if ((isset($_SESSION['permissions']) && ($_SESSION['permissions'] == 1))) { echo'<div class="delete topicdelete hover" title="delete">⨯</div>';}echo'</div></div>';
    echo "<!-- this is just so the radio buttons can change, it isn't actually inputed -->";
    echo "<input type='hidden' name='issticky' id='isstickyhiddenfield' value='" . $topic['sticky'] . "'>";
    echo "<input type='hidden' name='islocked' id='islockedyhiddenfield' value='" . $topic['locked'] . "'>";
    if ($topic['locked'] == '1') {$isitlockeddisplaymessage = true;} else {$isitlockeddisplaymessage = false;}

    $i = $begin;
    while($row = $query->fetch(PDO::FETCH_ASSOC))
    {
      echo '<div class="post" name="'.$row['post_id'].'">'
      .'<span class="tcore">'.($i+1)."</span>";
      if (($i == $begin) && ($i != 0)) {
        echo '<span class="tcore tup hover">▲</span>';
      }
      if (($i == $end) && ($i != $topic['topic_replies'] - 1)) {
        echo '<span class="tcore tdown hover">▼</span>';
      }
      echo "<span class='username' style='font-weight: bold;'>"; theusername($row['user_name'],$row['permissions']); echo ":</span> ";
      echo $row['post_text'].'<div class="postbuttons">';if ((isset($_SESSION['permissions']) && ($_SESSION['permissions'] == 1))) { echo'<div id="'.$row['post_id'].'" class="delete postdelete hover" title="delete">⨯</div>';}echo'</div></div>';
      $i++;
    }
    if ($isitlockeddisplaymessage == true) {
      echo "<div id='lockedmessage'>This topic is locked: You can not post in it unless you are a moderator.</div>";
    } else {}
  }
  else
  {
    $limit = 16;
    $begin = is_numeric($xbegin) ? ($xbegin - 1) : 0;
    $begin = max($begin, 0);
    $query = $db->query("SELECT topic_title, topic_replies, user_name, topic_id, sticky, locked FROM topics LEFT JOIN users ON topic_by = user_id ORDER BY sticky DESC, topic_score DESC LIMIT $begin, $limit");
    $cnt = $query->rowCount();
    if ($cnt < 1)
    {
      echo '<div id="left_title" begin=0 end=0 limit=0>Topics</div>';
      echo '<div class="topic"><p>None at the moment.</p></div>';
      die();
    }
    $end = $begin + $cnt - 1;
    echo '<div id="left_title" begin='.($begin+1).' end='.($end+1).' limit='.$limit.'>Topics</div>';
    $i = $begin;
    $first_topic = 0;
    while($row = $query->fetch(PDO::FETCH_ASSOC))
    {
      if ($i == $begin) $first_topic = $row['topic_id'];
      echo '<div class="topic'.(($i == $end)?' last':'').'" name="'.$row['topic_id'].'">'  
      .'<span class="tcore trep"'.(($row['topic_replies']<=0)?' style="display:none"':'').'>'.($row['topic_replies']).'</span>' . '<span class="stuck">' . ((($row['sticky'])==1)?'(Sticky)':'') . "</span>";
      if (($i == $begin) && ($i != 0)) {
        echo '<span class="tcore tup hover">▲</span>';
      }
      if (($i == $end) && ($cnt == $limit)) {
        echo '<span class="tcore tdown hover">▼</span>';
      }
      echo '<p>';displaytopiclocked($row['topic_title'],$row['locked']);echo '</p></div>';
      $i++;
    }
    return $first_topic;
  }
}
?>