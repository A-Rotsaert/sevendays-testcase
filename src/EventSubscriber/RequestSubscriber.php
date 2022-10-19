<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Andy Rotsaert <andy.rotsaert@live.be>
 */
final class RequestSubscriber implements EventSubscriberInterface
{
    private TokenStorageInterface $tokenStorage;
    private UrlGeneratorInterface $router;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param UrlGeneratorInterface $router
     */
    public function __construct(TokenStorageInterface $tokenStorage, UrlGeneratorInterface $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest'
        ];
    }

    /**
     * @param RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (($user = $this->tokenStorage->getToken()?->getUser())) {
            $this->checkProfileValues($user, $event);
            $this->changeLocaleOnProfilePage($event, $user);
        }
    }

    /**
     * If profile values are not set, redirect to the profile page, we need these values upon registration
     * @param UserInterface $user
     * @param RequestEvent $event
     * @return void
     */
    private function checkProfileValues(UserInterface $user, RequestEvent $event): void
    {
        if (!$user->getName() || !$user->getLocale() || !$user->getLocation()) {
            $route = $event->getRequest()->attributes->get('_route');
            if ($route && $route !== 'app_user_profile' && str_starts_with($route, 'app')) {
                $response = new RedirectResponse($this->router->generate('app_user_profile'));
                $response->send();
            }
        }
    }

    /**
     * If the user set his locale in the profile page, change it immediately
     * @param RequestEvent $event
     * @param UserInterface $user
     * @return void
     */
    private function changeLocaleOnProfilePage(RequestEvent $event, UserInterface $user): void
    {
        $request = $event->getRequest();
        if ($user->getName() && $user->getLocale() && $user->getLocation() && $request->getMethod(
        ) === 'POST' && $request->attributes->get(
            '_route'
        ) === 'app_user_profile') {
            $requestedLocale = $request->request->get('user_profile_form')['locale'];
            $compareLocale = $user->getLocale();
            if ($requestedLocale !== $compareLocale) {
                $request->setLocale($requestedLocale);
                $response = new RedirectResponse(
                    $this->router->generate($request->attributes->get('_route'), ['_locale' => $requestedLocale])
                );
                $response->send();
            }
        }
    }
}
