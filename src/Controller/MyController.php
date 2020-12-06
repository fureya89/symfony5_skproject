<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Service\PhotoVisibilityService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MyController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class MyController extends AbstractController
{
    /**
     * @Route("my/photos", name="my_photos")
     */
    public function index(){
        $entityManager = $this->getDoctrine()->getManager();
        $myPhotos = $entityManager->getRepository(Photo::class)->findBy(['user'=>$this->getUser()]);

        return $this->render('my/index.html.twig',['myPhotos'=>$myPhotos]);
    }

    /**
     * @Route ("my/photos/set_visibility/{id}/{visibility}", name="my_photos_set_visibility")
     * @param PhotoVisibilityService $photoVisibilityService
     * @param int $id
     * @param bool $visibility
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function myPhotoChangeVisibility (PhotoVisibilityService $photoVisibilityService, int $id, bool $visibility){

        $messages = [
            '0'=>'prywatne',
            '1'=>'publiczne'
        ];

        if($photoVisibilityService->makeVisible($id, $visibility)){
            $this->addFlash('success', 'Zdjęcie zostało ustawione jako'.$messages[$visibility].'.');
        } else {
            $this->addFlash('error', 'Wystąpił problem przy ustawianiu jako'.$messages[$visibility].'.');
        }

        return $this->redirectToRoute('my_photos');
    }

    /**
     * @Route("my/photos/remove/{id}", name="my_photos_remove")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function myPhotoRemove(int $id){
        $entityManager = $this->getDoctrine()->getManager();
        $myPhotoToRemove = $entityManager->getRepository(Photo::class)->find($id);

        if($myPhotoToRemove && $this->getUser() == $myPhotoToRemove->getUser()){
            try{
                $fileManager = new Filesystem();
                $fileManager->remove('images/hosting/'.$myPhotoToRemove->getFilename());
                if ($fileManager->exists('images/hosting'.$myPhotoToRemove->getFilename())){
                    $this->addFlash('error', 'Nie udało się usunąć zdjęcia.');
                } else {
                    $entityManager->remove($myPhotoToRemove);
                    $entityManager->flush();
                }

            }catch(\Exception $e) {
                $this->addFlash('error', 'Wystąpił problem podczas usuwania zdjęcia');
            }

        }else{
            $this->addFlash('error', 'Nie jesteś właścicielem zdjęcia - nie możesz go usunąć');
        }

        return $this->redirectToRoute('my_photos');
    }
}

