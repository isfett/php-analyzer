<?php

if (preg_match('/[;\"\s]{1}'.$key.'[\s]:[\s]([^;\"]*)/si', $attribs['style'], $attrval) AND isset($attrval[1]))
{}