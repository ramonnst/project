

<tr for="<?= $id ?>" <?php if ((empty($advanced) ? (false) : ($advanced)) == 'true') { ?> data-advanced="true"<?php } ?>>
    <td >
        <div class="control-label" for="<?= $id ?>">
            <?php if ((empty($help) ? (false) : ($help))) { ?>
                <a id="help_for_<?= $id ?>" href="#" class="showhelp"><i class="fa fa-info-circle"></i></a>
            <?php } elseif ((empty($help) ? (false) : ($help)) == false) { ?>
                <i class="fa fa-info-circle text-muted"></i>
            <?php } ?>
            <b><?= $label ?></b>
        </div>
    </td>
    <td >
        <?php if ($type == 'text') { ?>
            <input type="text" class="form-control" size="<?= (empty($size) ? ('50') : ($size)) ?>" id="<?= $id ?>" >
        <?php } elseif ($type == 'checkbox') { ?>
            <input type="checkbox" id="<?= $id ?>" >
        <?php } elseif ($type == 'select_multiple') { ?>
            <select multiple="multiple" <?php if ((empty($size) ? (false) : ($size))) { ?>size="<?= $size ?>"<?php } ?>  id="<?= $id ?>" <?php if ((empty($style) ? (false) : ($style))) { ?>class="<?= $style ?>" <?php } ?>  <?php if ((empty($hint) ? (false) : ($hint))) { ?>data-hint="<?= $hint ?>"<?php } ?>  <?php if ((empty($maxheight) ? (false) : ($maxheight))) { ?>data-maxheight="<?= $maxheight ?>"<?php } ?> data-width="<?= (empty($width) ? ('348px') : ($width)) ?>" data-allownew="<?= (empty($allownew) ? ('false') : ($allownew)) ?>" data-nbdropdownelements="<?= (empty($nbDropdownElements) ? ('10') : ($nbDropdownElements)) ?>"></select>
            <br/><a href="#" class="text-danger" id="clear-options" for="<?= $id ?>"><i class="fa fa-times-circle"></i></a> <small><?= $lang->_('Clear All') ?></small>
        <?php } elseif ($type == 'dropdown') { ?>
            <select <?php if ((empty($size) ? (false) : ($size))) { ?>size="<?= $size ?>"<?php } ?>  id="<?= $id ?>" class="<?= (empty($style) ? ('selectpicker') : ($style)) ?>"  data-width="<?= (empty($width) ? ('348px') : ($width)) ?>"></select>
        <?php } elseif ($type == 'password') { ?>
            <input type="password" class="form-control" size="<?= (empty($size) ? ('50') : ($size)) ?>" id="<?= $id ?>" >
        <?php } elseif ($type == 'textbox') { ?>
            <textarea rows="<?= (empty($height) ? ('5') : ($height)) ?>" id="<?= $id ?>"></textarea>
        <?php } elseif ($type == 'info') { ?>
            <span id="<?= $id ?>" ></span>
        <?php } ?>

        <?php if ((empty($help) ? (false) : ($help))) { ?>
            <small class="hidden" for="help_for_<?= $id ?>" ><?= $help ?></small>
        <?php } ?>
    </td>
    <td>
        <span class="help-block" for="<?= $id ?>" ></span>
    </td>
</tr>
