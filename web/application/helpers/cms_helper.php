<?php


function lang($line)
{
    $line = get_instance()->lang->line($line);
    return $line;
}

/**
 * Debug and die :)
 */
function dd($var)
{
   echo '<pre>';
   var_dump($var);
   echo '</pre>';
   die;
}

/**
 * Single quote escape for security reasons.
 *
 * @param	string
 * @return	string
 */
if(!function_exists('trim_and_clean'))
{
    function trim_and_clean($str)
    {
        return str_replace("'", "''", trim($str));
    }
}

/**
 * Building tree for nested comments.
 *
 * @param	[]
 * @param	int
 * @return	[]
 */

 if(!function_exists('build_hierarchy'))
 {
    function build_hierarchy($list, $reply_id = 0) {
        $newList = [];
        foreach($list as $key => $element) {
            if((int)$element['reply_id']  == $reply_id)
            {   
                if($children = build_hierarchy($list, $element['id']))
                {
                    $element['comments'] =  $children;
                }
                $newList[] = $element;
            }
        }
    
        return $newList;
    }
 }
