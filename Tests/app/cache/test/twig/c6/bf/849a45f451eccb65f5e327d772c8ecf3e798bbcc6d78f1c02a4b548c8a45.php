<?php

/* ::globals.html.twig */
class __TwigTemplate_c6bf849a45f451eccb65f5e327d772c8ecf3e798bbcc6d78f1c02a4b548c8a45 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["kitpages_data_grid"]) ? $context["kitpages_data_grid"] : null), "grid"), "default_twig"), "html", null, true);
    }

    public function getTemplateName()
    {
        return "::globals.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
