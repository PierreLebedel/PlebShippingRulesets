<?php

namespace PlebWooCommerceShippingRulesets;

class Rule
{
    private $id;

    public function __construct()
    {
    }

    public static function create()
    {
        $instance = new self();
        $instance->setId(uniqid());
        return $instance;
    }

    public static function createFromArray(array $ruleArray): self
    {
        $instance = new self();
        $instance->setId($ruleArray['id']);
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

    public function getRuleset(): Ruleset
    {
        return new Ruleset('test read from child');
    }

    public function htmlRender(string $fieldKey): string
    {
        ob_start();

        ?><li style="padding:5px 10px;margin:0;border:1px solid blue;">
            <h4>Ici ma règle #<?php echo $this->getId(); ?></h4>
            <input type="number" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>" reradonly>
        </li><?php

        return ob_get_clean();
    }

}
