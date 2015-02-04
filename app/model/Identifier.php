<?php namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Identifier extends Eloquent {

    public static function IdentifierFromDictionary($dict)
    {
        $identifier = new Identifier;
        $identifier->docset_name = $dict['docset_name'];
        $identifier->docset_filename = $dict['docset_filename'];
        $identifier->docset_platform = $dict['docset_platform'];
        $identifier->docset_bundle = $dict['docset_bundle'];
        $identifier->docset_version = $dict['docset_version'];
        $identifier->page_path = $dict['page_path'];
        $identifier->trim();
        return $identifier;
    }

    public function trim()
    {
        $docset_filename = $this->docset_filename;
        $docset_filename = preg_replace('/\\.docset$/', '', $docset_filename);
        $docset_filename = preg_replace('/[0-9]+\.*[0-9]+(\.*[0-9]+)*/', '', $docset_filename); // remove versions
        $docset_filename = trim(str_replace(range(0,9),'',$docset_filename)); // remove all numbers
        $this->docset_filename = $docset_filename;
        
        $page_path = $this->page_path;
        $basename = basename($page_path);
        $page_path = substr($page_path, 0, strlen($page_path)-strlen($basename));
        $page_path = preg_replace('/[0-9]+\.*[0-9]+(\.*[0-9]+)*/', '', $page_path); // remove versions
        $page_path = preg_replace('/[0-9]+_*[0-9]+(_*[0-9]+)*/', '', $page_path); // remove retarded versions that use _ instead of . (SQLAlchemy!)
        $page_path = str_replace(range(0,9),'',$page_path); // remove all numbers
        $page_path = trim(str_replace('//', '/', $page_path));
        $page_path .= $basename;
        $this->page_path = $page_path;
    }

    public function find_in_db()
    {
        $toMatch = ['docset_filename' => $this->docset_filename,
                    'page_path' => $this->page_path,
                   ];
        return Identifier::where($toMatch)->first();
    }

    public function entries()
    {
        return $this->hasMany('App\Entry');
    }
}