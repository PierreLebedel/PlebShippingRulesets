<?php

namespace PlebWooCommerceShippingRulesets;

class Ruleset
{
    private $id;
    private $name;
    private $order = null;
    private $rules = [];

    public function __construct()
    {
    }

    public static function create(): self
    {
        $instance = new self();
        $instance->setId(uniqid());
        $instance->setName(__("Ruleset", 'pleb').' #'.$instance->getId());
        return $instance;
    }

    public static function createFromArray(array $rulesetArray): self
    {
        $instance = new self();
        $instance->setId($rulesetArray['id']);
        $instance->setName($rulesetArray['name']);
        $instance->setOrder(!is_null($rulesetArray['order']) ? intval($rulesetArray['order']) : null);

        if (isset($rulesetArray['rules']) && is_array($rulesetArray['rules'])) {
            foreach ($rulesetArray['rules'] as $ruleArray) {
                $rule = Rule::createFromArray($ruleArray);
                $instance->addRule($rule);
            }
        }

        return $instance;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): string
    {
        if (empty($this->id)) {
            return uniqid();
        }
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        if(empty($this->name)) {
            return __("Ruleset", 'pleb').' #'.$this->getId();
        }
        return $this->name;
    }

    public function setOrder(?int $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function addRule(Rule $rule): self
    {
        $this->rules[] = $rule;
        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function htmlRender(string $fieldKey): string
    {
        ob_start();

        ?><div class="postbox pleb_ruleset">

            <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">
            <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][order]" value="<?php echo esc_attr($this->getOrder()); ?>" reradonly>

            <div class="pleb_title_input_wrapper">
                <div class="postbox-header">
                    <h2 class="hndle" title="<?php esc_attr_e("Move to change ruleset priority", 'pleb'); ?>">
                        <span>
                            <span class="dashicons dashicons-move"></span>
                            <?php echo $this->getName(); ?>
                        </span>
                    </h2>
                    
                    <div class="pleb_input_wrapper" style="display:none;height:30px;padding:3px 12px;">
                        <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][name]" value="<?php echo esc_attr($this->getName()); ?>" placeholder="<?php esc_attr_e("Nom du groupe de règles", "pleb"); ?>" required>
                    </div>
                    
                    <div class="handle-actions" style="padding-right:12px;">
                        <button class="button button-small pleb_edit_ruleset_button"><?php _e("Edit", 'pleb'); ?></button>
                    </div>
                </div>
            </div>

            <div class="inside" style="margin-bottom:0;">
                <div class="main">
                    <div class="ruleset_rules">
                    <?php $rules = $this->getRules();
                    if(empty($rules)): ?>
                        <div class="notice inline pleb_no_ruleset_rule_notice" style="margin-top:0;"><p><?php _e("No rule in this ruleset yet.", 'pleb'); ?></p></div>
                    <?php else: ?>
                        <?php foreach($rules as $rule): ?>
                            <?php echo $rule->htmlRender($fieldKey.'['.$this->getId().'][rules]');?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
                    
                    <div class="plugins">

                        <button type="button" class="button button-primary pleb_ruleset_add_rule_button" data-field_key="<?php echo $fieldKey.'['.$this->getId().'][rules]'; ?>"><?php _e("Add new rule", 'pleb'); ?></button>

                        <a href="#" class="delete" style="float:right;margin-top:6px;"><?php _e("Delete ruleset", 'pleb'); ?></a>
                        
                    </div>
                </div>
            </div>
        </div><?php

        return ob_get_clean();
    }
}
