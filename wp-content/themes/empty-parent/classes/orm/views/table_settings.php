<?php
extract( $viewbag );
?>
<div class="wrap">
    <h1>Table Settings</h1>

    <h3>Table Name: <?php echo $table_name; ?></h3>
    <form id="table_settings" method="post">
        <input name="table_name" type="hidden" value="<?php echo $table_name; ?>">
        <div class="form_description">
            <h2>Declare column properties</h2>
        </div>
        <table>
            <thead>
            <tr>
                <th>Column Name</th>
                <th>Type</th>
                <th>Default</th>
                <th>Null</th>
                <th>Index</th>
                <th>Auto Increment</th>
            </tr>
            </thead>
            <tbody>
			<?php
			for ( $i = 0; $i < $columns_number; $i ++ ) {
				?>
                <tr id="li_<?php echo $i; ?>">
                    <td>
                        <div>
                            <input id="element_<?php echo $i; ?>_column_name"
                                   name="data[<?php echo $i; ?>][column_name]"
                                   class="element text medium"
                                   type="text"
                                   maxlength="255"
                                   value="">
                        </div>
                    </td>
                    <td>
                        <div>
							<?php if ( $i ) { ?>
                                <select id="element_<?php echo $i; ?>_type" name="data[<?php echo $i; ?>][type]"
                                        class="element text medium">
                                    <option value="int">int</option>
                                    <option value="float">float</option>
                                    <option value="varchar">varchar</option>
                                    <option value="timestamp">timestamp</option>
                                </select>
							<?php } else {
								?>
                                <input id="element_<?php echo $i; ?>_type" name="data[<?php echo $i; ?>][type]"
                                       type="hidden"
                                       value="int">int
								<?php
							} ?>
                        </div>
                    </td>
                    <td>
                        <div>
							<?php if ( $i ) { ?>
                                <select id="element_<?php echo $i; ?>_default" name="data[<?php echo $i; ?>][default]"
                                        class="element text medium">
                                    <option value=""></option>
                                    <option value="define">define</option>
                                    <option value="null">null</option>
                                    <option value="current_timestamp">current_timestamp</option>
                                </select>
                                <input id="element_<?php echo $i; ?>-define"
                                       name="data[<?php echo $i; ?>]['default_define']" class="element text medium"
                                       type="text"
                                       maxlength="255"
                                       value="">
							<?php } else {
								?>
                                <input id="element_<?php echo $i; ?>_default" name="data[<?php echo $i; ?>][default]"
                                       type="hidden"
                                       value="">
								<?php
							} ?>
                        </div>
                    </td>
                    <td>
                        <div>
							<?php if ( $i ) { ?>
                                <input id="element_<?php echo $i; ?>_null" name="data[<?php echo $i; ?>][nullable]"
                                       class="element text medium"
                                       type="checkbox"
                                       value="">
							<?php } else {
								?>
                                <input id="element_<?php echo $i; ?>_null" name="data[<?php echo $i; ?>][nullable]"
                                       type="hidden"
                                       value="">
								<?php
							} ?>
                        </div>
                    </td>
                    <td>
                        <div>
							<?php if ( $i ) { ?>
                                <select id="element_<?php echo $i; ?>_index" name="data[<?php echo $i; ?>][index]"
                                        class="element text medium">
                                    <option value=""></option>
                                    <option value="unique">unique</option>
                                    <option value="index">index</option>
                                </select>
							<?php } else {
								?>
                                <input id="element_<?php echo $i; ?>_index" name="data[<?php echo $i; ?>][index]"
                                       type="hidden"
                                       value="primary">primary
								<?php
							} ?>
                        </div>
                    </td>
                    <td>
                        <div>
							<?php if ( $i ) { ?>
                                <input id="element_<?php echo $i; ?>_auto_increment"
                                       name="data[<?php echo $i; ?>][auto_increment]"
                                       class="element text medium"
                                       type="checkbox"
                                       value="">
							<?php } else {
								?>
                                <input id="element_<?php echo $i; ?>_auto_increment"
                                       name="data[<?php echo $i; ?>][auto_increment]"
                                       class="element text medium"
                                       type="hidden"
                                       value="on">
                                yes
								<?php
							} ?>
                        </div>
                    </td>
                </tr>
				<?php
			}
			?>
            </tbody>
        </table>
        <ul>
            <li class="buttons">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'custom_tables' ); ?>">
                <input type="hidden" name="action" value="table_create">
                <input id="saveForm" class="button_text" type="submit" name="submit" value="Submit">
            </li>
        </ul>
    </form>
</div>