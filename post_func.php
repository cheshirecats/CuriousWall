<?php
function post_get($db, $xtopic, $xbegin)
{
	if (is_numeric($xtopic) && (is_numeric($xbegin)))
	{
		$limit = 20;
		$query = $db->prepare("SELECT topic_title, topic_text, topic_replies, topic_by, user_name FROM topics LEFT JOIN users ON topic_by = user_id WHERE topic_id = ?");
		$query->execute(array($xtopic));
		if ($query->rowCount() < 1) {
			echo '<div id="topic_title" name=0 topic_replies=0 begin=0 end=0 limit=0>主题不存在</div>';
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

		$query = $db->prepare("SELECT post_text, post_id, post_by FROM posts WHERE post_topic = ? ORDER BY post_date ASC LIMIT $begin, $limit");
		$query->execute(array($xtopic));
		
		$end = $query->rowCount() + $begin - 1;
		
		echo '<div id="topic_title" name='.$xtopic.' topic_replies='.$topic['topic_replies']
			.' begin='.($begin+1).' end='.($end+1).' limit='.$limit.'>'.$topic['topic_title'].'</div>';
		echo '<div id="topic_text">'.$topic['topic_text'].'</div>';

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
			echo $row['post_text'].'</div>';
			$i++;
		}
	}
	else
	{
		$limit = 16;
		$begin = is_numeric($xbegin) ? ($xbegin - 1) : 0;
		$begin = max($begin, 0);
		$query = $db->query("SELECT topic_title, topic_replies, user_name, topic_id FROM topics LEFT JOIN users ON topic_by = user_id ORDER BY topic_score DESC LIMIT $begin, $limit");
		$cnt = $query->rowCount();
		if ($cnt < 1)
		{
			echo '<div id="left_title" begin=0 end=0 limit=0>主题列表</div>';
			echo '<div class="topic"><p>无主题</p></div>';
			die();
		}
		$end = $begin + $cnt - 1;
		echo '<div id="left_title" begin='.($begin+1).' end='.($end+1).' limit='.$limit.'>主题列表</div>';
		$i = $begin;
		$first_topic = 0;
		while($row = $query->fetch(PDO::FETCH_ASSOC))
		{
			if ($i == $begin) $first_topic = $row['topic_id'];
			echo '<div class="topic'.(($i == $end)?' last':'').'" name="'.$row['topic_id'].'">'
			.'<span class="tcore trep"'.(($row['topic_replies']<=0)?' style="display:none"':'').'>'.($row['topic_replies']).'</span>';
			if (($i == $begin) && ($i != 0)) {
				echo '<span class="tcore tup hover">▲</span>';
			}
			if (($i == $end) && ($cnt == $limit)) {
				echo '<span class="tcore tdown hover">▼</span>';
			}
			echo '<p>'.$row['topic_title'].'</p></div>';
			$i++;
		}
		return $first_topic;
	}
}
?>