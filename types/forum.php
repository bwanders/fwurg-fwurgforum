<?php
/**
 * fwurgforum plugin, forum type
 *
 * @author  Brend Wanders <b.wanders@xs4all.nl>
 */

class plugin_strata_type_forum extends plugin_strata_type {
    function normalize($value, $hint) {
        // split into thread and post
        list($thread, $post) = explode('#',$value,2);

        // thread is not numeric, return full value
        // post is not numeric, return full value
		if(intval($thread)==0 || (!empty($post) && intval($post)==0)) return $value;

        // construct result
        $result = intval($thread);
        if(!empty($post)) $result .= '#'.intval($post);

        return $result;
    }

    function render($mode, &$R, &$T, $value, $hint) {
        if($mode == 'xhtml') {
            list($thread,$post) = explode('#',$value,2);

						if(intval($thread) == 0 || (!empty($post) && intval($post)==0)) {
							$R->doc .= htmlentities($value);
							return true;
						}

						global $db;
						$thread = intval($thread);
						$query = 'SELECT t.*, f.* FROM phpbb_topics AS t, phpbb_forums AS f WHERE t.forum_id = f.forum_id AND t.topic_id = ' . $thread;
						$topics_result = $db->sql_query($query);
						$topics = array();
						while($topic = $db->sql_fetchrow($topics_result)) {
							$topics[] = $topic;
							break;
						}
						$db->sql_freeresult($topics_result);

						if(!count($topics)) {
							$R->doc .= htmlentities($value);
							return true;
						} else {
							global $phpbb_root_path, $phpEx;
							$topic = $topics[0];
							$link = "https://www.fwurg.net/phpbb/viewtopic.$phpEx?f={$topic['forum_id']}&t={$topic['topic_id']}";
							if(!empty($post)) $link .= "&p=$post#p$post";
							$title = html_entity_decode($topic['topic_title']);
							$R->externallink($link,$title);
						}

            return true;
        }

        return false;
    }

    function getInfo() {
        return array(
            'desc'=>'Links to the FWURG forum by thread id. (Refer to post with \'topic#post\'.)',
            'tags'=>array('numeric')
        );
    }
}
