<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 9/23/18
 * Time: 10:43 PM
 */

namespace App\Infrastructure\DB;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Id\IncrementGenerator;

class AlphaNumericGenerator extends IncrementGenerator
{
    public $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public $maxChars = 2;


    /** @inheritDoc
     */
    public function generate(DocumentManager $dm, $document)
    {
        $id = parent::generate($dm, $document);
        $index = strlen($this->chars);
        $out = $id;
        for ($i = 0; $i < $this->maxChars; $i++) {
            $out .= $this->chars[rand(0, $index - 1)];
        }
        return $out;
    }
}