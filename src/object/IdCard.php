<?php

namespace lgdz\object;

class IdCard
{
    private $region_code = '';
    private $birthday = '';
    private $age = '';
    private $gender = '';

    public function __construct(array $idCardAnalysis)
    {
        $this->region_code = $idCardAnalysis['region_code'] ?? '';
        $this->birthday = $idCardAnalysis['birthday'] ?? '';
        $this->age = $idCardAnalysis['age'] ?? '';
        $this->gender = $idCardAnalysis['gender'] ?? '';
    }

    /**
     * @return string
     */
    public function getRegionCode(): string
    {
        return $this->region_code;
    }

    /**
     * @return string
     */
    public function getBirthday(): string
    {
        return $this->birthday;
    }

    /**
     * @return string
     */
    public function getAge(): string
    {
        return $this->age;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }
}