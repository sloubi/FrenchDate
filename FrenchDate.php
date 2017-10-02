<?php

// setlocale(LC_TIME, "fr_FR");
setlocale(LC_TIME, 'fr_FR.utf8','fra');

class FrenchDate extends DateTime
{
    public function __construct($time = "now", DateTimeZone $timezone = NULL)
    {
        // Le constructeur accepte que $time soit un objet DateTime
        if (is_object($time) && (get_class($time) == 'DateTime' || get_class($time) == 'DateTimeImmutable'))
        {
            $time = $time->format('Y-m-d H:i:s.u');
        }

        parent::__construct($time, $timezone);
    }

    public function __toString()
    {
        return $this->strftime('%d %B %Y');
    }

    /**
     * Renvoie la date dans le format pris en compte par strftime()
     * @param  string $format Format de strftime()
     * @return string
     */
    public function strftime($format)
    {
        return strftime($format, $this->getTimestamp());
    }

    /**
     * Renvoie la liste des jours fériés pour l'année
     * @param  boolean $year L'année à prendre en compte, ou false : l'année actuelle
     * @return array
     */
    public static function getPublicHolidays($year = false)
    {
        // Par défaut, on demande les jours fériés de l'année en cours
        if (!$year) $year = date('Y');

        $publicHolidays = [];

        // 1er janvier
        $publicHolidays[] = new FrenchDate('1st January');

        // Lundi de Pâques
        $easter = new DateTimeImmutable(date('Y-m-d', easter_date($year)));
        $publicHolidays[] = $easter->modify('+1 day');

        // 1er mai
        $publicHolidays[] = new FrenchDate('1st May');

        // 8 mai
        $publicHolidays[] = new FrenchDate('8th May');

        // Ascension (39 jours après paques)
        $publicHolidays[] = new FrenchDate($easter->modify('+39 days'));

        // Pentecôte (50j après pâques)
        $publicHolidays[] = new FrenchDate($easter->modify('+50 days'));

        // 14 juillet
        $publicHolidays[] = new FrenchDate('14th July');

        // 15 août
        $publicHolidays[] = new FrenchDate('15th August');

        // Toussaint
        $publicHolidays[] = new FrenchDate('1st November');

        // Armistice
        $publicHolidays[] = new FrenchDate('11th November');

        // Noël
        $publicHolidays[] = new FrenchDate('25th December');

        return $publicHolidays;
    }

    /**
     * Est-ce un jour férié ?
     * @return boolean
     */
    public function isPublicHoliday()
    {
        $publicHolidays = self::getPublicHolidays();
        return in_array($this, $publicHolidays);
    }

    /**
     * Est-ce un jour ouvré ?
     * @return boolean
     */
    public function isBusinessDay()
    {
        $noBusinessDay = [6, 7];
        return !$this->isPublicHoliday() && !in_array($this->format('N'), $noBusinessDay);
    }

    /**
     * Renvoie la date du prochain ouvré
     * @param  boolean $days Ajoute un délai en jour
     * @return object FrenchDate
     */
    public function getNextBusinessDay($days = false)
    {
        $date = clone $this;

        $date->modify('+1 day');

        if ($days && is_int($days))
        {
            $date->modify('+' . $days-1 . ' days');
        }

        while (!$date->isBusinessDay())
        {
            $date->modify('+1 day');
        }

        return $date;
    }

}
