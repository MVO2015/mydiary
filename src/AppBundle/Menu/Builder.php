<?php
namespace AppBundle\Menu;

use AppBundle\Entity\User;
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

        // access services from the container!
//           $em = $this->container->get('doctrine')->getManager();
        // findMostRecent and Blog are just imaginary examples
//           $blog = $em->getRepository('AppBundle:Blog')->findMostRecent();
//
//           $menu->addChild('Latest Blog Post', array(
//               'route' => 'blog_show',
//               'routeParameters'))) => array('id' => $blog->getId())
//           ));

        $menu->addChild('Notes', ['route' => 'paginate']);
        $menu->addChild('Index', ['route' => 'index']);
        $menu->addChild('Tags')->setAttribute('dropdown',true)
            ->setUri('#')
            ->setLinkAttributes(['data-toggle' => 'dropdown', 'class' => 'dropdown-toggle'])
            ->setChildrenAttribute('class', 'dropdown-menu')
            ->addChild('Index', ['route' => 'tag_index']);
        $menu->addChild('Add', ['route' => 'tag_new']);
        $menu->addChild('Expand/Collapse', ['route' => 'collapse']);
        $menu->addChild('＋', ['route' => 'add']);

        /** @var User $user */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if (is_a($user, User::class)) {
            $menu->addChild($user->getUsername(), ['route' => 'fos_user_profile_show']);
        } else {
            $menu->addChild("Login", ['route' => 'fos_user_security_login'])
                ->setAttributes(['class' => 'pull-xs-right']);
        }

        return $menu;
    }
}
