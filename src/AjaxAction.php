<?php

namespace PlebWooCommerceShippingRulesets;

use PlebWooCommerceShippingRulesets\Models\Rule;
use PlebWooCommerceShippingRulesets\Models\Ruleset;

class AjaxAction
{
    public function __construct()
    {

    }

    public static function register(string $hook, bool $public = false)
    {
        $instance = new self();

        if(!method_exists($instance, $hook)) {
            throw new \Exception(sprintf(__("Unknown ajax action: %s", 'pleb'), $hook));
        }

        if($public) {
            add_action('wp_ajax_nopriv_'.$hook, [$instance, $hook]);
        }
        add_action('wp_ajax_'.$hook, [$instance, $hook]);
    }

    private function checkParameters(array $params = [])
    {
        $missingParameters = [];
        if(!empty($params)) {
            foreach($params as $param) {
                if(!isset($_REQUEST[$param]) || empty($_REQUEST[$param])) {
                    $missingParameters[] = $param;
                }
            }
        }
        if(!empty($missingParameters)) {
            throw new \Exception(sprintf(__("Ajax action missing params: %s", 'pleb'), implode(', ', $missingParameters)));
        }
    }

    public function pleb_ruleset_template()
    {
        $this->checkParameters(['field_key']);

        $newRuleset = Ruleset::create();
        echo $newRuleset->htmlRender($_REQUEST['field_key']);

        if(defined('DOING_AJAX') && DOING_AJAX) {
            die();
        }
    }

    public function pleb_ruleset_rule_template()
    {
        $this->checkParameters(['field_key']);

        $newRule = Rule::create();
        echo $newRule->htmlRender($_REQUEST['field_key']);

        if(defined('DOING_AJAX') && DOING_AJAX) {
            die();
        }
    }

    public function pleb_ruleset_delete()
    {
        $this->checkParameters(['ruleset_id']);

        die('@todo');

        if(defined('DOING_AJAX') && DOING_AJAX) {
            die();
        }
    }



}
