<?php

namespace App\Controller;

use App\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Routing\Annotation\Route;

class MyController extends AbstractController
{
    /**
     * @Route("my/photos", name="my_photos")
     */
    public function index(){

    }

    /**
     * @Route("my/photos/set_private/{id}", name="my_photos_set_as_private")
     * @param int $id
     */
    public function myPhotoSetAsPrivate(int $id){
        $entityManager = $this->getDoctrine()->getManager();
        $myPhoto = $entityManager->getRepository(Photo::class)->find($id);
        if($this->getUser()){
            if($this->getUser() == $myPhoto->getUser()){
                try{
                    $myPhoto->setIsPublic(0);
                    $entityManager->persist($myPhoto);
                    $entityManager->flush();
                    $this->addFlash('success', 'Zdjęcie zostało ustawione jako prywatne');

                }catch(\Exception $e){
                    $this->addFlash('error', 'Wystąpił problem przy ustawianiu jako prywatne');
                }
            }else{
                $this->addFlash('error', 'Nie jesteś właścicielem tego zdjecia');
            }
        }else{
            $this->addFlash('error', 'Nie jesteś zalogowany!');
        }

        return $this->redirectToRoute('latest_photos');
    }

    /**
     * @Route("my/photos/set_public/{id}", name="my_photos_set_as_public")
     * @param int $id
     */
    public function myPhotoSetAsPublic(int $id){
        $entityManager = $this->getDoctrine()->getManager();
        $myPhoto = $entityManager->getRepository(Photo::class)->find($id);

        if($this->getUser()){
            if($this->getUser() == $myPhoto->getUser()){
                try{
                    $myPhoto->setIsPublic(1);
                    $entityManager->persist($myPhoto);
                    $entityManager->flush();
                    $this->addFlash('success', 'Zdjęcie zostało ustawione jako publiczne');

                }catch(\Exception $e){
                    $this->addFlash('error', 'Wystąpił problem przy ustawianiu jako publiczne');
                }
            }else{
                $this->addFlash('error', 'Nie jesteś właścicielem tego zdjecia');
            }
        }else{
            $this->addFlash('error', 'Nie jesteś zalogowany!');
        }


        return $this->redirectToRoute('latest_photos');
    }

    /**
     * @Route("my/photos/remove/{id}", name="my_photos_remove")
     * @param int $id
     */
    public function myPhotoRemove(int $id){
        $entityManager = $this->getDoctrine()->getManager();
        $myPhotoToRemove = $entityManager->getRepository(Photo::class)->find($id);
        if($this->getUser()){
            if($this->getUser() == $myPhotoToRemove->getUser()){
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
        }else{
            $this->addFlash('error', 'Nie jesteś zalogowany!');
        }
        return $this->redirectToRoute('latest_photos');
    }
}

