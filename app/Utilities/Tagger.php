<?php

namespace App\Utilities;

use App\Models\Tag;

class Tagger{

    public static function tagCafe( $cafe, $tags ){
        /*
          Iterate over all of the tags attaching each one
          to the cafe.
        */
        foreach( $tags as $tag ){
            /*
              Generates a tag name for the entered tag
            */
            $name = self::generateTagName( $tag );


            /*
              Get the tag by name or create a new instance of tag which is not saved to database.
            */
            $newCafeTag     = \App\Models\Tag::firstOrNew( array('name' => $name ) );

            /*
              Confirm the cafe tag has the name we provided. If it's set already
              because the tag exists, this won't make a difference.
            */
            $newCafeTag->tag = $name;

            $newCafeTag->save();

            /*
              Apply the tag to the cafe
            */
            $cafe->tags()->syncWithoutDetaching( [ $newCafeTag->id => ['user_id' => Auth::user()->id ] ] );
        }
    }

    private static function generateTagName( $tagName ){

        //  Trim whitespace from beginning and end of tag
        $name = trim( $tagName );

        $name = strtolower( $name );

        //  Convert anything not a letter or number to a dash.
        $name = preg_replace( '/[^a-zA-Z0-9]/', '-', $name );


        //  Remove multiple instance of '-' and group to one.
        $name = preg_replace( '/-{2,}/', '-', $name );

        //  Get rid of leading and trailing '-'
        $name = trim( $name, '-' );


        //  Returns the cleaned tag name
        return $name;
    }
}