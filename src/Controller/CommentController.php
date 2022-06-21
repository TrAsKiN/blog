<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Core\Service\FlashService;
use Blog\Form\CommentForm;
use Exception;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class CommentController extends Controller
{
    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    #[Route('/blog/post/comment/{slug}', name: 'comment_add', restricted: true)]
    public function commentAdd(
        string $slug,
        CommentForm $commentForm,
        FlashService $messages
    ): ResponseInterface {
        if ($commentForm->form->isPost() && $commentForm->form->isValid()) {
            if ($commentForm->getResult($slug)) {
                $messages->addFlash(
                    "Votre commentaire a été enregistré ! Un administrateur le validera prochainement.",
                    'success'
                );
            }
        }
        return $this->redirect('post', compact('slug'));
    }
}
