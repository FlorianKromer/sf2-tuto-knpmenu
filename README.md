
# Tutoriel de mise en place d'un menu pas-à-pas


Tous nos sites utilisent des menus.

Il existe plusieurs façons de faire :

1.  A la main. C'est rapide pour des menus simplistes, mais ce n'est plus maintenable dès que le menu se complique, qu'il dépend d'un contexte. 
2.  En utilisant un bundle dédié à cet usage : KnpMenuBundle (il en existe sans doute un paquet d'autres)

Pourquoi lui ?

*   Pourquoi pas ?
*   Utilisé dans Sonata, donc déjà présent sur quasi toutes nos installations.
*   Renommé et on a déjà travailler avec !
*   Permet de générer tous les menus du site de façon unifiée.
*   De nombreuses fonctionnalités 
*   Personnalisable et adaptable au besoin

Voila ! Plus arbitraire que cela je n'ai pas !

Voyons le détail en quelques étapes. Certains d'entre vous peuvent sans doute en sauter, 
mais ça ne fait pas de mal une petite piqure de rappel de temps à autre.


Note avant de commencer :

J'ai installé SF 2.5.0 avec la nouvelle répartition des répertoires.

*   bin : contient les binaires ```console``` compris !
*   var : contient toutes les données variables (logs, cache, bootstrap,…)

Aussi certaines commandes sont à adapter.

Quelques infos en plus : 

*   What is the new Symfony 3 directory structure? : http://stackoverflow.com/questions/23993295/what-is-the-new-symfony-3-directory-structure
*   Upgrading a Symfony application to 2.5 : https://gist.github.com/nicwortel/0c938aa77c5bd4fde064


## Étape 1 : Installation 

    ➜  git clone https://github.com/jerome-fix/sf2-tuto-knpmenu.git
    ➜  git checkout -f step1-initialisation
    ➜  composer install -v

Pour l'instant rien à voir. C'est un SF2 en version 2.5 de base. On peut répondre par 
défaut à toutes les questions de l'installation.

## Étape 2 : Ajout du bundle knpmenu

    ➜ git checkout -f step2-install-bundle
    ➜ composer install -v

On a simplement ajouter le bundle dans le [composer.json](https://github.com/jerome-fix/sf2-tuto-knpmenu/commit/c3c27e4feef57226e87499f206039428735cbfb4) et [appKernel.php](https://github.com/jerome-fix/sf2-tuto-knpmenu/commit/36fda1ed9ef7d4e68c290118ba36dd7c292dc307). 
La procédure complète est dispo ici : https://github.com/KnpLabs/KnpMenuBundle/blob/master/Resources/doc/index.md#installation

Note : On installe la version 2 ! La version 1 est appelée à disparaître bientôt.


## Étape 3 : Création du menu au travers d'un service (à privilégier)

    ➜ git checkout -f step3-create-menu-service

On rentre dans le vif du sujet.

cf. la documentaton du bundle : https://github.com/KnpLabs/KnpMenuBundle/blob/master/Resources/doc/index.md#create-your-first-menu

### Création des services pour les menus


    <service id="menu_core.menu_builder" class="Menu\CoreBundle\Menu\MenuBuilder">
        <argument type="service" id="knp_menu.factory" />
    </service>

    <service id="menu_core.menu.primary" class="Knp\Menu\MenuItem"
        factory-service="menu_core.menu_builder" factory-method="createPrimaryMenu" scope="request">
        <argument type="service" id="request" />
        <tag name="knp_menu.menu" alias="primary" />
    </service>

Le premier « menu_core.menu_builder » est le service qui nous servira tout du long pour manipuler le menu.

Le second est à répéter pour chaque menu indépendant que nous voudrions créer: primary, secondary, sidebar,…

Les points importants sont :

*   factory-method : méthode à appeler
*   tag knp_menu.menu


### Création du menu 

    public function createPrimaryMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', array('route' => 'menu_core_homepage'));
        $menu->addChild('Menu1', array('route' => 'menu_core_menu1'));
        $menu->addChild('Menu2', array('route' => 'menu_core_menu1'));
        $menu->addChild('About', array('route' => 'menu_core_about'));
        $menu->addChild('Help', array('route' => 'menu_core_help'));

        return $menu;
    }


Rien de sorcier la dedans : un label, une route. Il est bien sûr possible de passer une url externe :

    $menu->addChild('nvision', array('uri' => 'http://www.nvision.lu'))->setAttribute('target', "_blank");


## Étape 4 : Menus en cascade


    ➜ git checkout -f step4-add-submenu

On se retrouve donc avec le code suivant dans le Builder :

    public function createPrimaryMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', array('route' => 'menu_core_homepage'));
        $menu->addChild('Menu1', array('route' => 'menu_core_menu1'));
        $menu->addChild('Menu2', array('route' => 'menu_core_menu1'));
        $about = $menu->addChild('About', array('route' => 'menu_core_about'));
        $about->addChild('Help', array('route' => 'menu_core_help'));
        $about->addChild('Search', array('route' => 'menu_core_search'));

        return $menu;
    }

Si l'on regarde le code généré on constate que tout y est !

1. L'arborescence
2. les classes qui vont bien : current +  current_ancestror


    <ul>
        <li class="current first"><a href="/app_dev.php/">Home</a></li>
        <li><a href="/app_dev.php/menu1">Menu1</a></li>
        <li><a href="/app_dev.php/menu1">Menu2</a></li>
        <li class="current_ancestor last">
            <a href="/app_dev.php/about">About</a>                
            <ul class="menu_level_1">
                <li class="first"><a href="/app_dev.php/about/help">Help</a></li>
                <li class="current last"><a href="/app_dev.php/about/search">Search</a></li>
            </ul>
        </li>
    </ul>

### Étape 5 : Jouer avec les paramètres de configuration (View)

    ➜ git checkout -f step5-customize+menu-1

Il est facile d'adapter un certains nombres de paramètres au moment du rendu, notamment les 
classes pour s'adapter aux besoins du design.

Voir la doc pour le rendu des menus.

Par exemple dans l'affichage :

    {{ knp_menu_render('primary', {ancestorClass: 'ma-classe-a-moi'}) }}


Si dessous la liste complète de la configuration utilisable (disponible dans la doc) :

*   depth
*   matchingDepth: The depth of the scan to determine whether an item is an ancestor of the current item. 
*   currentAsLink (default: true) 
*   currentClass (default: current) 
*   ancestorClass (default: current_ancestor) 
*   firstClass (default: first) 
*   lastClass (default: last) 
*   compressed (default: false) 
*   allow_safe_labels (default: false) 
*   clear_matcher (default true): whether to clear the internal cache of the matcher after rendering 
*   leaf_class (default: null): class for leaf elements in your html tree 
*   branch_class (default: null): class for branch elements in your html tree 



### Étape 6 : Personnaliser le rendu

    ➜ git checkout -f step6-change-renderer


Plusieurs façons : 

#### General :

    # app/config/config.yml
    knp_menu:
        twig:  
            template: knp_menu.html.twig #(modifier ici)
            
#### The easy way : indiquer un template au moment du rendu

    {{ knp_menu_render('primary', {template: 'my_menu.html.twig'}) }}


#### The other way : créer un rendu spécifique : « primary » ici.


Création du service :

On reprend presque celui de base (seul le template change ici !)

    <service id="menu_core.menu.primary_renderer" class="Menu\CoreBundle\Menu\PrimaryRender">
        <tag name="knp_menu.renderer" alias="primary" />
        <argument type="service" id="twig" />
        <argument>MenuCoreBundle:Menu:primary.html.twig</argument>
        <argument type="service" id="knp_menu.matcher" />
        <argument>%knp_menu.renderer.twig.options%</argument>
    </service>

Création de la classe de rendu (totalement personnalisable, même si ici rien n'est nécessaire)

    <?php // src/Menu/CoreBundle/Menu/PrimaryRender.php
    namespace Menu\CoreBundle\Menu;
    
    use Knp\Menu\Renderer\TwigRenderer;
    
    class PrimaryRender extends TwigRenderer
    {}

Au niveau du template

    {{ knp_menu_render('primary', {…}, 'primary') }}


Plus d'infos : https://github.com/KnpLabs/KnpMenu/blob/master/doc/02-Twig-Integration.markdown



### Étape 7 : Personnalisation des items

    ➜ git checkout -f step7-personnalisation

#### Ajouter des attributs aux items de menu.

    public function createPrimaryMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        […]

        $about = $menu->addChild('About', array('route' => 'menu_core_about'))
                        ->setAttribute('class', 'dropdown')
                        ->setChildrenAttribute('class', 'dropdown-menu');       
        […]

        return $menu;
    }

### Étape 8 : Internationalisation


    ➜ git checkout -f step8-internationalisation
    ➜ composer install -v
    

Je préfère passer les labels et les traduire directement dans la méthode de création de menu plutôt que 
de déléguer cela à la vue.
Cela permet de gérer facilement la spécialisation (pluralisation, contextualisation,…) en cas de besoin.

On ajoute le traducteur à notre classe en tant que service

    <service id="menu_core.menu_builder" class="Menu\CoreBundle\Menu\MenuBuilder">
        <argument type="service" id="knp_menu.factory" />
        <argument type="service" id="translator" />
    </service>


Puis dans la classe Menu\CoreBundle\Menu\MenuBuilder.php

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory, $translator)
    {
        $this->factory = $factory;
        $this->translator = $translator;
    }
    
	
    
Ensuite on modifie la création des Items.

    $about = $menu->addChild('menu_core.menu.about', array('route' => 'menu_core_about'))
                ->setLabel($this->translator->trans('menu_core.menu.about', array(), 'menu' ));

Après avoir installé jms/translation-bundle il ne reste qu'à extraire les chaines à traduire.

    bin/console translation:extract --bundle=MenuCoreBundle   -v en
    
    

Voilà. Quelques pistes pour mettre en place de manière simple les menus sous Symfony 2.
Je vous invite bien sûr à ne pas vous contenter de ces quelques infos, mais de continuer la lecture des docs.

# Documentation 

*   https://github.com/KnpLabs/KnpMenu/tree/master/doc
*   https://github.com/KnpLabs/KnpMenuBundle/blob/master/Resources/doc/index.md


## Cookbook :

*   Utiliser les événements pour étendre dynamiquement un menu :  https://github.com/KnpLabs/KnpMenuBundle/blob/master/Resources/doc/events.md
*   Custom provider : https://github.com/KnpLabs/KnpMenuBundle/blob/master/Resources/doc/custom_provider.md
*   Custom renderer : https://github.com/KnpLabs/KnpMenuBundle/blob/master/Resources/doc/custom_renderer.md
*   Custom KnpMenuBundle navigation bar twig template to support Font Awesome icons & Twitter bootstrap layout : https://gist.github.com/nielsmouthaan/3765766
*   La liste complète : https://github.com/KnpLabs/KnpMenuBundle/tree/master/Resources/doc

## Divers : 

*   Symfony2 advanced menus (User roles) : http://www.trisoft.ro/blog/6-symfony2-advanced-menus
