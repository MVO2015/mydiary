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

        $menu->addChild('Add', array('route' => 'add'));
        $menu->addChild('Home', array('route' => 'homepage'));

        // access services from the container!
//           $em = $this->container->get('doctrine')->getManager();
        // findMostRecent and Blog are just imaginary examples
//           $blog = $em->getRepository('AppBundle:Blog')->findMostRecent();
//
//           $menu->addChild('Latest Blog Post', array(
//               'route' => 'blog_show',
//               'routeParameters' => array('id' => $blog->getId())
//           ));

        $menu->addChild('Diary', array('route' => 'index'));
        $menu->addChild('Tag Index', ['route' => 'tag_index']);
        $menu->addChild('Tag Collection', ['route' => 'tag_collection']);

        // ... add more children

        return $menu;
    }
}
