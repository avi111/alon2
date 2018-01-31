<div class="wrap">
    <h1>Custom Tables</h1>

    <form id="initial_form" method="post">
        <div class="form_description">
            <h2>First,</h2>
        </div>
        <ul>
            <li id="li_0">
				<?php global $wpdb; ?>
                <label class="description" for="element_0">Prefix <?php echo $wpdb->prefix; ?> </label>
                <div>
                    <input id="element_0" name="prefix" class="element text medium" type="checkbox" checked/>
                </div>
            </li>
            <li id="li_1">
                <label class="description" for="element_1">Table Name </label>
                <div>
                    <input id="element_1" name="table_name" class="element text medium" type="text" maxlength="255"
                           value="">
                </div>
            </li>
            <li id="li_2" class="">
                <label class="description" for="element_2">Number of columns </label>
                <div>
                    <input id="element_2" name="columns_number" class="element text medium" type="text" maxlength="255"
                           value="">
                </div>
            </li>

            <li class="buttons">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'custom_tables' ); ?>">
                <input type="hidden" name="action" value="table_settings">
                <input id="saveForm" class="button_text" type="submit" name="submit" value="Submit">
            </li>
        </ul>
    </form>
</div>