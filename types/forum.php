<?php
/**
 * fwurgforum plugin, forum type
 *
 * @author  Brend Wanders <b.wanders@xs4all.nl>
 */

class plugin_strata_type_forum extends plugin_strata_type {
    function normalize($value, $hint) {
				if(intval($value)==0) return $value;
        return intval($value);
    }

    function render($mode, &$R, &$T, $value, $hint) {
        if($mode == 'xhtml') {
						if(intval($value) == 0) {
							$R->doc .= htmlentities($value);
							return true;
						}

						global $db;
						$thread = intval($value);
						$query = 'SELECT t.*, f.* FROM phpbb_topics AS t, phpbb_forums AS f WHERE t.forum_id = f.forum_id AND t.topic_id = ' . $thread;
						$topics_result = $db->sql_query($query);
						$topics = array();
						while($topic = $db->sql_fetchrow($topics_result)) {
							$topics[] = $topic;
							break;
						}
						$db->sql_freeresult($topics_result);

						if(!count($topics)) {
							$R->doc .= $value;
							return true;
						} else {
							global $phpbb_root_path, $phpEx;
							$topic = $topics[0];
							$link = "http://fwurg.xs4all.nl/phpbb/viewtopic.$phpEx?f={$topic['forum_id']}&t={$topic['topic_id']}";
							$title = html_entity_decode($topic['topic_title']);
							$R->externallink($link,$title);
						}

            return true;
        }

        return false;
    }

    function getInfo() {
        return array(
            'desc'=>'Links to the FWURG forum by thread id.',
            'tags'=>array('numeric')
        );
    }
}
