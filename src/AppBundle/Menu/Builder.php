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

        $menu->addChild('Notes', ['route' => 'paginate'])->setAttribute('class', 'nav-item')->setLinkAttributes(['class' => 'nav-link']);
        $menu->addChild('Index', ['route' => 'index'])->setAttribute('class', 'nav-item')->setLinkAttributes(['class' => 'nav-link']);
        $menu->addChild('Tags')->setAttributes(['class' => 'nav-item dropdown'])
            ->setUri('#')
            ->setLinkAttributes([
                'data-toggle' => 'dropdown',
                'class' => 'dropdown-toggle nav-link',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false',
                'id'=>'navbarDropdownMenuLink'
            ])
            ->setChildrenAttribute('class', 'dropdown-menu')
            ->addChild('Index', ['route' => 'tag_index'])->setAttributes(['class' => 'dropdown-menu', 'aria-labelledby' => 'navbarDropdownMenuLink'])->setLinkAttributes(['class' => 'dropdown-item']);;
        $menu->addChild('Add', ['route' => 'tag_new'])->setAttribute('class', 'nav-item')->setLinkAttributes(['class' => 'nav-link']);
        $menu->addChild('Expand/Collapse', ['route' => 'collapse'])->setAttribute('class', 'nav-item')->setLinkAttributes(['class' => 'nav-link']);
        $menu->addChild('＋', ['route' => 'add'])->setAttribute('class', 'nav-item')->setLinkAttributes(['class' => 'nav-link']);

        /** @var User $user */
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if (is_a($user, User::class)) {
            $menu->addChild($user->getUsername(), ['route' => 'fos_user_profile_show'])->setAttribute('class', 'nav-item')->setLinkAttributes(['class' => 'nav-link']);
        } else {
            $menu->addChild("Login", ['route' => 'fos_user_security_login'])
                ->setAttributes(['class' => 'nav-item pull-xs-right'])->setLinkAttributes(['class' => 'nav-link']);
        }

        return $menu;
    }
}
