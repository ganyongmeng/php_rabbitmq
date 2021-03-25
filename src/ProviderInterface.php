<?php
/**
 * ProvderInterface
 */
namespace ganyongmeng\php_rabbitmq;
interface ProviderInterface
{

    public function process($msg = '');

}
