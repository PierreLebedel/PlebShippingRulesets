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
        $instance->setName("");
        return $instance;
    }

    public static function createFromArray(array $rulesetArray): self
    {
        $instance = new self();
        $instance->setId($rulesetArray['id']);
        $instance->setName($rulesetArray['name']);
        $instance->setOrder(!is_null($rulesetArray['order']) ? intval($rulesetArray['order']) : null);

        foreach($rulesetArray['rules'] as $ruleArray) {
            $rule = Rule::createFromArray($ruleArray);
            $instance->addRule($rule);
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
        if(empty($this->id)) {
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

        ?><div class="postbox">
            <h2 class="hndle ui-sortable-handle"><span><?php _e("Ruleset", 'pleb'); ?> #<?php echo $this->getId(); ?></span></h2>
            <div class="inside">
				<div class="main">
                    <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">
                    <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][name]" value="<?php echo esc_attr($this->getName()); ?>" required>
                    <input type="number" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][order]" value="<?php echo esc_attr($this->getOrder()); ?>" reradonly>

                    <?php $rules = $this->getRules();
                    if(empty($rules)): ?>
                        <div class="notice"><p><?php _e("No rule in this ruleset yet.", 'pleb'); ?></p></div>
                    <?php else: ?>
                        <ul>
                            <?php foreach($rules as $rule): ?>
                                <?php echo $rule->htmlRender($fieldKey.'['.$this->getId().'][rules]');?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <button type="button" class="button button-primary"><?php _e("Add new rule", 'pleb'); ?></button>
                </div>
            </div>
        </div><?php

        return ob_get_clean();
    }

}
