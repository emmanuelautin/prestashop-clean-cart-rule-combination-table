<?php

if (!defined('_PS_VERSION_'))
{
exit;
}

class CleanCartRuleCombinationTable extends Module
{
    public function __construct()
    {
        $this->name = 'cleancartrulecombinationtable';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Emmanuel Autin';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Clean Cart Rule Combination Table');
        $this->description = $this->l('This module will delete unused data from ps_cart_rule_combination table.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('actionAdminControllerSetMedia');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookActionAdminControllerSetMedia()
    {
        $this->cleanTable();
    }


    private function deleteUnusedCartRuleCombination( ){

        try{

            $db = Db::getInstance();

          $sql = "DELETE T1 FROM "._DB_PREFIX_."cart_rule_combination T1
                    LEFT JOIN "._DB_PREFIX_."cart_rule T2 ON T2.id_cart_rule = T1.id_cart_rule_1
                    WHERE T2.id_cart_rule IS NULL";

                $db->execute($sql);

            return true;

        }catch(Exception $e){
            throw new Exception($this->l('An error occurred while cleaning the table:').$e->getMessage());
        }

    }

    private function cleanTable()
    {

        try {

           // DELETE FROM `ps_cart_rule_combination` WHERE id_cart_rule_1 NOT IN ( SELECT id_cart_rule FROM `ps_cart_rule` ) OR id_cart_rule_2 NOT IN ( SELECT id_cart_rule FROM `ps_cart_rule` )
            $db = Db::getInstance();
            $sql = "DELETE FROM " . _DB_PREFIX_ . "cart_rule_combination
            WHERE (id_cart_rule_1 IN (SELECT id_cart_rule FROM " . _DB_PREFIX_ . "cart_rule WHERE active = " . $db->escape(0) . ")
                OR id_cart_rule_2 IN (SELECT id_cart_rule FROM " . _DB_PREFIX_ . "cart_rule WHERE active = " . $db->escape(0) . "))
            LIMIT 100000;";
            $db->execute($sql);
            return true;
        } catch (Exception $e) {
            throw new Exception($this->l('An error occurred while cleaning the table:').$e->getMessage());
        }

    }

    public function getContent( )
    {
        $output = '';
        $token = Tools::getAdminTokenLite('AdminModules');
        $form_url = $this->context->link->getAdminLink('AdminModules', true) . "&configure={$this->name}&token={$token}";
        $this->context->smarty->assign('form_url', $form_url);

        if (Tools::isSubmit('clean_table')) {

            try {

                if($this->cleanTable( )){
                    $this->context->controller->confirmations[] = $this->l('Table cleaned successfully.');
                }

            } catch (Exception $e) {
                $output .=  $this->context->controller->errors[] = $e->getMessage();
            }

        } elseif (Tools::isSubmit('clean_table_more')) {

            try {

                if($this->deleteUnusedCartRuleCombination( )){
                    $this->context->controller->confirmations[] = $this->l('Table cleaned more successfully.');
                }

            } catch (Exception $e) {
                $output .=  $this->context->controller->errors[] = $e->getMessage();
            }
        }

        return $output . $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }
}
