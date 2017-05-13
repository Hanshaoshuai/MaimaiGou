<?php

namespace Pingpp\Error;

class ParamsError extends Base
{
    public function __construct($message)
    {        
        parent::__construct($message);
        //$this->param = $param;
    }
}
