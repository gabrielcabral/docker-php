<?php

namespace ZendServerGateway\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Exception\RuntimeException;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\Controller\AbstractController;
use ZendServerGateway\Controller\ParameterDataContainer;

class RouteParam extends AbstractPlugin
{
    public function __invoke($param = null, $default = null)
    {
        $controller = $this->getController();

        if (!$controller instanceof InjectApplicationEventInterface) {
            throw new RuntimeException(
                'Controllers must implement Zend\Mvc\InjectApplicationEventInterface to use this plugin.'
            );
        }

        if ($controller instanceof AbstractController) {
            $parameterData = $controller->getEvent()->getParam('ZendServerGatewayParameterData');
            if ($parameterData instanceof ParameterDataContainer) {
                return $parameterData->getRouteParam($param, $default);
            }
        }

        return $controller->getEvent()->getRouteMatch()->getParam($param, $default);

    }
}