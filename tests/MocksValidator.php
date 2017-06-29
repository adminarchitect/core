<?php

trait MocksValidator
{
    public function mockValidator()
    {
        $validator = $this->createMock(\Illuminate\Contracts\Validation\Validator::class);

        \Terranet\Administrator\Form\Element::setValidator(
            $validator
        );
    }
}