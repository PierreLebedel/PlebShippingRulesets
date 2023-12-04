<div class="postbox pleb_ruleset">

            <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">
            <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][order]" value="<?php echo esc_attr($this->getOrder()); ?>" reradonly>

            <h2 class="hndle ui-sortable-handle">
                <span><?php _e("Ruleset", 'pleb'); ?> #<?php echo $this->getId(); ?></span>
                <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][name]" value="<?php echo esc_attr($this->getName()); ?>" placeholder="<?php esc_attr_e("Ruleset name", "pleb"); ?>" required>
            </h2>
            <div class="inside">
				<div class="main">
                    <div class="ruleset_rules">
                    <?php $rules = $this->getRules();
            if(empty($rules)): ?>
                        <div class="notice inline" style="margin-top:0;"><p><?php _e("No rule in this ruleset yet.", 'pleb'); ?></p></div>
                    <?php else: ?>
                        <ul>
                            <?php foreach($rules as $rule): ?>
                                <?php echo $rule->htmlRender($fieldKey.'['.$this->getId().'][rules]');?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    </div>
                    
                    <div class="plugins">

                        <button type="button" class="button button-primary"><?php _e("Add new rule", 'pleb'); ?></button>

                        <a href="#" class="delete"><?php _e("Delete ruleset", 'pleb'); ?></a>
                        
                    </div>
                </div>
            </div>
        </div>