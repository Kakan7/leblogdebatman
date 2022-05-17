<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'=>'Adresse Email',
                'attr'=>[
                    'placeholder'=>'exemple@exemple.com',
                ],
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Ce champ est obligatoire',
                    ]),
                    new Email([
                        'message'=>'L\'adresse email {{ value }} n\'est pas une adresse email valide.'
                    ]),
                ],
            ] )
            ->add('plainPassword', RepeatedType::class, [
                'mapped'=>false,
                'type'=>PasswordType::class,
                'invalid_message'=>'Le mot de passe ne correspond pas à sa confirmation',
                'first_options'=>[
                    'label'=>'Mot de passe',
                ],
                'second_options'=>[
                    'label'=>'Confirmation du mot de passe',
                ],
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Merci de renseigner un mot de passe',
                    ]),
                    new Length([
                        'min'=>8,
                        'max'=>4096,
                        'minMessage'=>'Votre mot de passe doit contenir au moins {{ limit }} caractéres',
                        'maxMessage'=>'Votre mot de passe est trop grand',
                    ]),
                    new Regex([
                        'pattern'=>'/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{8,4096}$/u',

                        'message'=>'Votre mot de passe doit contenir obligatoirement au moins une minuscule, une majuscule, un chiffre et un caractére special',
                    ]),
                ],
            ])

            ->add('pseudonym', TextType::class,[

                'label'=>'Pseudonyme',
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Merci de renseigner un pseudonyme',
                    ]),
                    new Length([
                        'min'=>2,
                        'max'=>40,
                        'minMessage'=>'Votre pseudonyme doit contenir au moins {{ limit }} caractéres',
                        'maxMessage'=>'Votre pseudonyme doit contenir au maximum {{ limit }} caractéres',
                    ]),

                ],

            ])

            ->add('save', SubmitType::class,[
                'label'=>'Créer mon compte',
                'attr'=>[
                    'class'=>'btn btn-outline-primary w-100'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            //TODO: enlever le novalidate à la fin
            'attr'=>[
                'novalidate'=>'novalidate'
            ]
        ]);
    }
}