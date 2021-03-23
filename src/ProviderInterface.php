<?php

namespace Rabbitmq;

interface ProviderInterface
{

    public function process($msg = '');

}
