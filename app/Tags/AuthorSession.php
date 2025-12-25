<?php

namespace App\Tags;

use Statamic\Tags\Tags;
use Statamic\Facades\Asset;
use Statamic\Facades\Entry;

class AuthorSession extends Tags
{
	protected static $handle = 'author_session';

	/**
     * Handle the {{ author_session }} tag
     */
    public function index()
    {
        // Get session data
        $sessionData = $this->getSessionData();

        // If there's content wrapped inside the tag, process it
        $content = $this->parseContent($this->content());

        // Combine session data and wrapped content
        return $sessionData . $content;
    }

    /**
     * Handle {{ author_session:check }}
     */
    public function check()
    {
        return session()->has('author_logged_in') ? true : false;
    }

    /**
     * Handle {{ author_session:name }}
     */
    public function name()
    {
        return session()->get('author_name', '');
    }

    /**
     * Handle {{ author_session:email }}
     */
    public function email()
    {
        return session()->get('author_email', 'No email found');
    }

    /**
     * Handle {{ author_session:id }}
     */
    public function id()
    {
        return session()->get('author_id', '');
    }

    /**
     * Handle {{ author_session:avatar }}
     */
    public function slug()
    {
        return session()->get('author_slug', '');
    }

    /**
     * Handle {{ author_session:city }}
     */
    public function city()
    {
        return session()->get('author_city', '');
    }

    /**
     * Handle {{ author_session:address }}
     */
    public function address()
    {
        return session()->get('author_address', '');
    } 

    /**
     * Handle {{ author_session:state }}
     */
    public function state()
    {
        return session()->get('author_state', '');
    }

    /**
     * Handle {{ author_session:zip }}
     */
    public function zip()
    {
        return session()->get('author_zip', '');
    }

    /**
     * Handle {{ author_session:notes }}
     */
    public function notes()
    {
        return session()->get('author_notes', '');
    }
    
    /**
     * Get all author session data
     */
    protected function getSessionData()
    {
        return [
            'author_logged_in' => session()->has('author_logged_in'),
            'author_id' => session()->get('author_id', null),
            'author_slug' => session()->get('author_slug', null),
            'author_name' => session()->get('author_name', ''),
            'author_email' => session()->get('author_email', 'No email found'),
            'author_city' => session()->get('author_city', ''),
            'author_address' => session()->get('author_address', ''),
            'author_state' => session()->get('author_state', ''),
            'author_zip' => session()->get('author_zip', ''),
            'author_notes' => session()->get('author_notes', ''),
        ];
    }


    public function authorData()
    {
        $authorId = session()->get('author_id');

        if (!$authorId) {
            return '';
        }

        $author = Entry::query()
        ->where('collection', 'customer')
        ->where('id', $authorId)
        ->first();

        if (!$author) {
            return '';
        }
       
        return $author->get($field) ?? '';
    }
}