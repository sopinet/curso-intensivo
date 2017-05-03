<?php
namespace AppBundle\Twig;

class VariousExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('colorFrequency', array($this, 'colorFrequencyFilter')),
        );
    }

    public function colorFrequencyFilter($porcentage)
    {
        if ($porcentage >= 0 && $porcentage <= 1) {
            $color = "#000000";
        } else if ($porcentage > 1 && $porcentage <= 10) {
            $color = "#AA0000";
        } else if ($porcentage > 10 && $porcentage <= 30) {
            $color = "#FF7514";
        } else {
            $color = "#0000AA";
        }

        return $color;
    }
}