<?php

namespace Laranoia\Permissions\Traits;

trait ChecksValidity
{
    public function validate(): bool
    {
        $now = new \DateTimeImmutable();
        if($this->valid_from !== null && $this->valid_from > $now){
            //TODO throw exception
        }
        if($this->valid_until !== null && $this->valid_until < $now){
            //TODO throw exception
        }
        return true;
    }
}