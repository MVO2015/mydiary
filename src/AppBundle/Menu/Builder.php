<?php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem(
            'root',
            [
                'childrenAttributes' => [
                    'class' => 'nav navbar-nav',
                ],
            ]
        );

        $menu->addChild('＋', array('route' => 'add'));

        // access services from the container!
//           $em = $this->container->get('doctrine')->getManager();
        // findMostRecent and Blog are just imaginary examples
//           $blog = $em->getRepository('AppBundle:Blog')->findMostRecent();
//
//           $menu->addChild('Latest Blog Post', array(
//               'route' => 'blog_show',
//               'routeParameters'))) => array('id' => $blog->getId())
//           ));

        $menu->addChild('Index', array('route' => 'index'));
        $menu->addChild('Pages', array('route' => 'paginate'));
        $menu->addChild('Tags')->setAttribute('dropdown',true)
            ->setUri('#')
            ->setLinkAttributes(['data-toggle' => 'dropdown', 'class' => 'dropdown-toggle'])
            ->setChildrenAttribute('class', 'dropdown-menu')
            ->addChild('Index', ['route' => 'tag_index'])->getParent()
            ->addChild('Add', ['route' => 'tag_new']);

        // ... add more children

        return $menu;
    }
}
