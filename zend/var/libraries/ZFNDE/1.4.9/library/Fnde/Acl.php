<?php

class Fnde_Acl extends Zend_Acl {

    protected $_roles_key = 'roles';
    protected $_resources_key = 'resources';
    protected $_privileges_key = 'privileges';
    protected $_all_privileges_key = '[ALL]';
    protected $_privileges_separator = NULL;
    protected $_module_controller_separator = NULL;

    public function __construct($config, $module_controller_separator = '-', $privileges_separator = ',') {
        $this->setPrivilegesSeparator($privileges_separator);
        $this->setModuleControllerSeparator($module_controller_separator);

        if ($config instanceof Zend_Config) {
            if (!is_null($config->{$this->_roles_key})) {
                $this->initRoles($config->{$this->_roles_key}->toArray());
            }
            if (!is_null($config->{$this->_resources_key})) {
                $this->initResources($config->{$this->_resources_key}->toArray());
            }
            if (!is_null($config->{$this->_privileges_key})) {
                $this->setAccess($config->{$this->_privileges_key}->toArray());
            }
        } elseif (is_array($config)) {
            if (array_key_exists($this->_roles_key, $config)) {
                $this->initRoles($config[$this->_roles_key]);
            }
            if (array_key_exists($this->_resources_key, $config)) {
                $this->initResources($config[$this->_resources_key]);
            }
            if (array_key_exists($this->_privileges_key, $config)) {
                $this->setAccess($config[$this->_privileges_key]);
            }
        }
    }

    private function initRoles($roles) {
        foreach ($roles as $role => $properties) {
            if (intval($properties['enabled']) == 1) {
                if (!$this->hasRole($role)) {
                    $this->addRole(new Zend_Acl_Role($role), isset($properties['parents']) ? $properties['parents'] : array());
                }
            }
        }
    }

    private function initResources($resources) {
        foreach ($resources as $resource) {
            if (!$this->has($resource['id'])) {
                $this->add(new Zend_Acl_Resource($resource['id']), isset($resource['parent']) ? $resource['parent'] : NULL);
            }
        }
    }

    private function setAccess($privileges) {
        foreach ($privileges as $role => $resources) {

            //set allow rules
            if (array_key_exists('allow', $resources)) {
                if (is_array($resources['allow'])) {
                    foreach ($resources['allow'] as $resource => $privileges) {
                        $prvarr = (strtoupper($privileges) != $this->_all_privileges_key) ? array_map('trim', explode($this->_privileges_separator, $privileges)) : NULL;
                        $this->allow($role, str_replace($this->_module_controller_separator, ':', trim($resource)), $prvarr);
                    }
                } else {
                    if (strtoupper($resources['allow']) == $this->_all_privileges_key) {
                        $this->allow($role);
                    }
                }
            }

            //set deny rules
            if (array_key_exists('deny', $resources)) {
                if (is_array($resources['deny'])) {
                    foreach ($resources['deny'] as $resource => $privileges) {
                        $prvarr = (strtoupper($privileges) != $this->_all_privileges_key) ? array_map('trim', explode($this->_privileges_separator, $privileges)) : NULL;
                        $this->deny($role, str_replace($this->_module_controller_separator, ':', trim($resource)), $prvar);
                    }
                } else {
                    if (strtoupper($resources['deny']) == $this->_all_privileges_key) {
                        $this->deny($role);
                    }
                }
            }
        }
    }

    public function setPrivilegesSeparator($privileges_separator) {
        $this->_privileges_separator = $privileges_separator;
    }

    public function setModuleControllerSeparator($module_controller_separator) {
        $this->_module_controller_separator = $module_controller_separator;
    }

    /**
     * Método para configurar o Zend_Navigation_Container de acordo com as
     * regras definidas no ACL, preenchendo os atributos module, controller e
     * privilege dos Zend_Navigation_Page_Mvc contidos no container
     * e retorna o container atualizado com os recursos e privilégios
     *
     * @param Zend_Navigation_Container $container
     * @return Zend_Navigation_Container
     */
    public function prepareAclNavigation(Zend_View_Helper_Navigation &$navigation) {

        $iterator = new RecursiveIteratorIterator($navigation->getContainer(),
                        RecursiveIteratorIterator::CHILD_FIRST);

        $oldDepth = 0;
        $isActive = array();

        // iterate container
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $parentLabel = ($page->getParent() instanceof Zend_Navigation_Page) ? $page->getParent()->getLabel() : 'root';

            if (empty($isActive[$depth])) {
                $isActive[$depth] = 0;
            }

            if ($page instanceof Zend_Navigation_Page_Mvc) {
                $resource = $page->getModule() . ':' . $page->getController();
                $privilege = $page->getAction();

                if ($this->has($resource)) {

                    if ($this->isAllowed(trim($navigation->getRole()), $resource, $privilege)) {
                        $isActive[$depth]++;
                    }
                    $page->setResource($resource);
                    $page->setPrivilege($privilege);
                }
            }

            if ($oldDepth !== $depth) {
                if ($oldDepth > $depth) {
                    $page->setVisible($isActive[$depth + 1]);
                    $isActive[$depth] += $isActive[$depth + 1];
                    $isActive[$depth + 1] = 0;
                }
            }

            if ($depth == 0) {
                $isActive[0] = 0;
            }

            $oldDepth = $depth;
        }
        return $container;
    }

    public function migraAcl ()
    {
        $content .= "[desenv]\n\n;------------------------------------------------------------------------------\n; PERFIS DO SISTEMA\n;------------------------------------------------------------------------------\n\n";

        // ROLES
        foreach($this->getRoles() as $role) {
            if ($role !== 'guest') {
                $content .= str_pad("resources.acl.roles.$role", 60, " ", STR_PAD_RIGHT)
                         . " = " . key($this->_getRoleRegistry()->getParents($role))."\n";
            }
        }
        $content .= "\n;------------------------------------------------------------------------------\n; RECURSOS\n;------------------------------------------------------------------------------\n\n";

        // acesso completo
        foreach($this->_rules['allResources']['byRoleId'] as $roles => $privileges) {
            $content .= str_pad("resources.acl.allow.*", 60, " ", STR_PAD_RIGHT)
                     . " = $roles\n";
        }

        // recursos e privilegios
        foreach($this->_rules['byResourceId'] as $resource => $roles) {
            foreach ($roles['byRoleId'] as $role => $privilege) {
                // Tem todos os privileges!
                if (isset($privilege['allPrivileges'])) {
                    // verifica se é deny ou allow
                    $permissao = ($privilege['allPrivileges']['type'] == 'TYPE_ALLOW') ? 'allow' : 'deny';
                    $permissoes["resources.acl.$permissao.$resource.*"][] = $role;
                } else {
                    // cada privilegio dentro daquele resource
                    foreach ($privilege['byPrivilegeId'] as $privilege => $type) {
                        $permissao = ($type['type'] == 'TYPE_ALLOW') ? 'allow' : 'deny';
                        $permissoes["resources.acl.$permissao.$resource.$privilege"][] = $role;
                    }
                }
            }
        }

        foreach ($permissoes as $recurso => $roles) {
            $content .= str_pad($recurso, 60, " ", STR_PAD_RIGHT)
                     . " = ".implode(',', $roles)."\n";
        }

        $content .= "\n\n[homolog : desenv]\n\n[producao : desenv]\n\n";

//        echo "<PRE>" , $content;

        $file = APPLICATION_CONFIGS . DIRECTORY_SEPARATOR . 'acl_novo.ini';

        file_put_contents($file, $content);

    }

}