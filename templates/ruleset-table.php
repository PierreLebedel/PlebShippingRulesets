<div class="pleb_ruleset">
            <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">
            <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][name]" value="<?php echo esc_attr($this->getName()); ?>" placeholder="<?php esc_attr_e("Nom du groupe de règles", "pleb"); ?>" required>
            <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][order]" value="<?php echo esc_attr($this->getOrder()); ?>" reradonly>

            <table class="wc-shipping-zone-methods widefat">
                <thead>
                    <tr>
                        <td class="wc-shipping-zone-method-sort hndle">

                        </td>
                        <th class="wc-shipping-zone-method-title" colspan="3">
                            <?php _e("Ruleset", 'pleb'); ?> #<?php echo $this->getId(); ?>
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="4">

                            <button type="button" class="button button-primary"><?php _e("Add new rule", 'pleb'); ?></button>
                            <a href="#" class="wc-shipping-zone-method-delete"><?php _e("Delete ruleset", 'pleb'); ?></a>

                        </td>
                    </tr>
                </tfoot>
                <tbody class="wc-shipping-zone-method-rows">
                    <?php $rules = $this->getRules();
            if(empty($rules)): ?>
                        <tr>
                            <td colspan="4">
                                <div class="notice inline" style="margin-top:0;"><p><?php _e("No rule in this ruleset yet.", 'pleb'); ?></p></div>
                            </td>
                        </tr>
                        
                    <?php else: ?>
                        <?php foreach($rules as $rule): ?>
                            <tr>
                                <td colspan="4">
                                    <?php echo $rule->htmlRender($fieldKey.'['.$this->getId().'][rules]');?>
                                </td>
                            </tr>
                            
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <br>
        </div>