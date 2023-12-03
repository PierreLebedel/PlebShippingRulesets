<div class="pleb_ruleset">
            
            <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">
            <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][order]" value="<?php echo esc_attr($this->getOrder()); ?>" reradonly>

            <table class="widefat">
                <thead>
                    <tr>
                        <th>
                            <div class="hndle">>Déplacer</div>
                        </th>
                        <th class="row-title">
                            <span><?php echo $this->getName(); ?></span>
                                
                            <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][name]" value="<?php echo esc_attr($this->getName()); ?>" placeholder="<?php esc_attr_e("Nom du groupe de règles", "pleb"); ?>" required style="display:none;">
                        </th>
                        <th style="text-align:right;">
                            <button class="button button-small pleb_edit_ruleset_button"><?php _e("Modifier", 'pleb'); ?></button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rules = $this->getRules();
            if (empty($rules)) : ?>
                        <tr>
                            <td colspan="2">
                                <div class="notice inline" style="margin-top:0;">
                                    <p><?php _e("No rule in this ruleset yet.", 'pleb'); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($rules as $rule) : ?>
                            <tr>
                                <td colspan="2" class="row-title">
                                    <?php echo $rule->htmlRender($fieldKey.'['.$this->getId().'][rules]'); ?>
                                </td>
                                <td>actions</td>
                            </tr>
                        <?php endforeach; ?>
                        
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="row-title" colspan="2">
                        <div class="plugins">

                            <button type="button" class="button button-primary"><?php _e("Add new rule", 'pleb'); ?></button>

                            <a href="#" class="delete"><?php _e("Delete ruleset", 'pleb'); ?></a>

                            </div>
                        </th>
                    </tr>
                </tfoot>
            </table>
            <br>
        </div>