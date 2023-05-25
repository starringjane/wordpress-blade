<?php

namespace StarringJane\WordpressBlade;

class WordPressUtils
{
    /**
     * Check if a given key is a reserved query var
     * https://codex.wordpress.org/WordPress_Query_Vars
     */
    public static function isReservedQueryVar($key)
    {
        return in_array(
            $key,
            ['attachment','attachment_id','author','author_name','cat','calendar','category_name','comments_popup','cpage','day','error','exact','feed','hour','m','minute','monthnum','more','name','order','orderby','p','page_id','page','paged','pagename','pb','post_type','posts','preview','robots','s','search','second','sentence','static','subpost','subpost_id','taxonomy','tag','tag_id','tb','term','w','withcomments','withoutcomments','year']
        );
    }
}
