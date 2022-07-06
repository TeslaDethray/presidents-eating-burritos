<?php

/*********************
 * Lot Users
 *********************/
abstract class LotUser {
    const SIZE = 0;
    const TYPE = 'vehicle';
    use HasId;
}
class Car extends LotUser {
    const SIZE = 1;
    const TYPE = 'car';
}
class Motorcycle extends LotUser {
    const SIZE = 0;
    const TYPE = 'motorcycle';
}
class Van extends LotUser {
    const SIZE = 3;
    const TYPE = 'van';
}

/*********************
 * Parking Lot
 *********************/
class ParkingLot {
    /**
     * @var ParkingSpot[]
     */
    private $parking_spots = [];

    const TYPE_KEY = [
        CompactSpot::TYPE => CompactSpot::class,
        LargeSpot::TYPE => LargeSpot::class,
        MotorcycleSpot::TYPE => MotorcycleSpot::class,
    ];

    /**
     * @param $settings A hash with
     *    string ParkingSpot::type as its keys and
     *    int as its corresponding number of such instances in the lot
     */
    public function __construct(array $settings = [])
    {
        foreach ($settings as $spot_type => $number_of_spots) {
            for ($i = 0; $i < $number_of_spots; $i++) {
                $key = strval($i);
                $this->parking_spots[$key] = self::createNewSpot($key, $spot_type);
            }
        }
    }

    /**
     * @param string $id
     * @param string $type
     * @param LotUser $occupant
     * @return ParkingSpot
     */
    public static function createNewSpot(string $id, string $type, LotUser $occupant = null) : ParkingSpot
    {
        if(null === self::TYPE_KEY[$type]) {
            throw new Exception(
                "The type of spot {$type} is not available. Select from: " . implode(', ', array_keys(self::TYPE_KEY)),
                1
            );
        }
        $type_of_spot = self::TYPE_KEY[$type];
        return new $type_of_spot($id, $occupant);
    }

    /**
     * @param string|null $type The type of spot to return
     * @return string The ID of the first available spot
     * @throws Exception When no more spots are available
     */
    public function getFirstAvailableSpotId(string $type = null) : string
    {
        $spots = $this->getSpotsRemaining($type);
        if (empty($spots)) {
            throw new Exception("There are no spots of $type remaining.");
        }
        $spot = array_shift($spots);
        return $spot->id;
    }

    /**
     * @param string|null $type The type of spot to return
     * @return array The spots
     */
    public function getSpots(string|null $type = null) : array
    {
        $spots = $this->parking_spots;
        if ($type === null) {
            return $spots;
        }
        return array_filter(
            $spots,
            function ($spot) use ($type) {
                return $spot::TYPE === $type;
            }
        );
    }

    /**
     * @param string $type The type of user to filter by
     * @return array The spots occupied by this type of user
     */
    public function getUsers(string $type = null) : array
    {
        $users = [];
        $spots = $this->getSpotsByUserType($type);
        array_walk(
            $spots,
            function ($spot) use ($users) {
                $user = $spot->getOccupant();
                $users[$user->id] = $occupant;
            }
        );
        return $users;
    }

    /**
     * @param string $type The type of user to filter by
     * @return array The spots occupied by this type of user
     */
    public function getSpotsByUserType(string $type = null) : array
    {
        return array_filter(
            $this->getSpots(),
            function ($spot) use ($type) {
                return ($type === null) || ($spot->getOccupant()::TYPE === $type);
            }
        );
    }

    /**
     * @param string|null $type The type of spot to report on
     * @return array The number of occupied spots
     */
    public function getSpotsOccupied(string|null $type = null) : array
    {
        $spots = $this->getSpots($type);
        return array_filter(
            $spots,
            function ($spot) {
                return $spot->isOccupied();
            }
        );
    }

    /**
     * @param string|null $type The type of spot to report on
     * @return array The number of unoccupied spots
     */
    public function getSpotsRemaining(string|null $type = null) : array
    {
        $spots = $this->getSpots($type);
        return array_filter(
            $spots,
            function ($spot) {
                return !$spot->isOccupied();
            }
        );
    }

    /**
     * @return int The number of parking spots in this parking lot
     */
    public function getNumberOfSpots() : int
    {
        return count($this->parking_spots);
    }

    /**
     * @param string $type The type of user to count
     * @return int The number of spots occupied by users of this type
     */
    public function getNumberOfSpotsByUserType(string $type) : int
    {
        return count($this->getSpotsByUserType($type));
    }

    /**
     * @param string $type The type of spot to report on
     * @return int The number of unoccupied spots
     */
    public function getNumberOfSpotsRemaining(string $type = null) : int
    {
        return count($this->getSpotsRemaining($type));
    }

    /**
     * @param string $type The type of user to report on
     * @return int The number of users
     */
    public function getNumberOfUsers(string $type = null) : int
    {
        return count($this->getUsers($type));
    }

    /**
     * @param string $type The type of user to report on
     * @return bool
     */
    public function isFull(string $type = null) : bool
    {
        return $this->getNumberOfSpotsRemaining($type) === 0;
    }

    /**
     * @param LotUser $occupant The vehicle to be parked
     * @throws Exception When there is no space for this vehicle
     */
    public function parkUser(LotUser $occupant) : void
    {

        switch ($occupant::TYPE) {
            // Motorcycle: check moto, check compact, check large
            case Motorcycle::TYPE:
                try {
                    $id = $this->getFirstAvailableSpotId(MotorcycleSpot::TYPE);
                } catch (Exception $e) {
                    try {
                        $id = $this->getFirstAvailableSpotId(CompactSpot::TYPE);
                    } catch (Exception $e) {
                        try {
                            $id = $this->getFirstAvailableSpotId(LargeSpot::TYPE);
                        } catch (Exception $e) {
                            throw new Exception("The " . Motorcycle::TYPE . "cannot be parked because there are no spots remaining in the lot.");
                        }
                    }
                }
                $this->parking_spots[$id]->setOccupant($occupant);
                break;
            // Car: check compact, check large
            case Car::TYPE:
                try {
                    $id = $this->getFirstAvailableSpotId(CompactSpot::TYPE);
                } catch (Exception $e) {
                    try {
                        $id = $this->getFirstAvailableSpotId(LargeSpot::TYPE);
                    } catch (Exception $e) {
                        throw new Exception("The " . Car::TYPE . "cannot be parked because there are no spots of adequate size remaining in the lot.");
                    }
                }
                $this->parking_spots[$id]->setOccupant($occupant);
                break;
            // Van: check large, check compact (use 3)
            case Van::TYPE:
                try {
                    $id = $this->getFirstAvailableSpotId(LargeSpot::TYPE);
                    $this->parking_spots[$id]->setOccupant($occupant);
                } catch (Exception $e) {
                    if ($this->getNumberOfSpotsRemaining(CompactSpot::TYPE) < 3) {
                        throw new Exception("The " . Van::TYPE . "cannot be parked because there are not enough spots remaining in the lot.");
                    }
                    array_walk(
                        array_slice($this->getSpots(CompactSpot::TYPE), 0, 3),
                        function ($spot) {
                            $this->parking_spots[$spot->id]->setOccupant($occupant);
                        }
                    );
                }
                $this->parking_spots[$id]->setOccupant($occupant);
                break;
        }

    }

    /**
     * @param array $spots An array of ParkingSpot instances to count the capacity of
     * @return int The calclated remaining capacity of all spots of the requested type
     */
    private static function getParkingCapacity(array $spots) : int
    {
        return array_reduce(
            $spots,
            function ($carry, $spot) {
                $carry += $spot::SIZE;
                return $carry;
            },
            0
        );
    }
}

/*********************
 * Parking Spots
 *********************/
abstract class ParkingSpot {
    /**
     * @var bool True if multiple of this spot can be combined to park a single vehicle
     */
    const COMBINABLE = true;
    const SIZE = 0;
    const TYPE = 'spot';
    use HasId;
    /**
     * @var LotUser
     */
    private $occupant;

    /**
     * @param string $id
     * @param LotUser|null $occupant
     */
    public function __construct(string $id, LotUser $occupant = null)
    {
        $this->id = $id;
        if (!is_null($occupant)) {
            $this->setOccupant($occupant);
        }
    }

    /**
     * @param LotUser $lot_user Prospective parker
     * @return bool True if the spot can park this vehicle on its own
     */
    public function canPark(LotUser $lot_user) : bool
    {
        return $lot_user::SIZE <= self::SIZE;
    }

    /**
     * @return LotUser|null The occupant of this spot
     */
    public function getOccupant() : LotUser|null
    {
        return $this->occupant;
    }

    public function isOccupied() : bool
    {
        return null !== $this->getOccupant();
    }

    /**
     * @return LotUser The previous occupant of this spot
     * @throws Exception When the spot was already empty before running this function
     */
    public function removeOccupant() : LotUser
    {
        $occupant = $this->getOccupant();
        if (is_null($occoccupant)) {
            throw new Exception("The parking spot is empty. No occupant to remove.", 1);
        }
        $this->occupant = null;
        return $occupant;
    }

    /**
     * @param LotUser $occupant
     */
    public function setOccupant(LotUser $occupant) : void
    {
        $old_occupant = $this->getOccupant();
        if (null !== $old_occupant) {
            throw new Exception("The parking spot is not empty. A new occupant cannot be placed.", 1);
        }

        if (!$this->canPark($occupant)) {
            throw new Exception("A " . self::TYPE . " spot is not large enough to accomodate a " . $occupant::TYPE . ".");
        };

        $this->occupant = $occupant;
    }
}

class CompactSpot extends ParkingSpot {
    /**
     * @var bool True because multiple of this spot can be combined to park a single vehicle
     */
    const COMBINABLE = true;
    const SIZE = 1;
    const TYPE = 'compact';
}
class LargeSpot extends ParkingSpot {
    /**
     * @var bool True because multiple of this spot can be combined to park a single vehicle
     */
    const COMBINABLE = true;
    const SIZE = 3;
    const TYPE = 'large';
}
class MotorcycleSpot extends ParkingSpot {
    /**
     * @var bool False because multiple of this spot can NOT be combined to park a single vehicle
     */
    const COMBINABLE = false;
    const SIZE = 0;
    const TYPE = 'motorcycle';
}

/*********************
 * Traits
 *********************/

trait HasId {
    /**
     * @var string
     */
    public $id;

    /**
     * An ID to identify the vehicle instance by
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
}

/*********************
 * Run It
 *********************/

// Parking Lot Configuration
$num_compact_spots = rand(0, 10);
$num_large_spots = rand(0, 10);
$num_motorcycle_spots = rand(0, 10);

// User Stats
$num_cars = rand(0, 10);
$num_motorcycles = rand(0, 10);
$num_vans = rand(0, 10);

echo "Creating a new parking lot with " . $num_compact_spots . " compact spots," . PHP_EOL;
echo "Creating a new parking lot with " . $num_large_spots . " large spots," . PHP_EOL;
echo "Creating a new parking lot with " . $num_motorcycle_spots . " motorcycle spots," . PHP_EOL;

$lot = new ParkingLot([
    CompactSpot::TYPE => $num_compact_spots,
    LargeSpot::TYPE => $num_large_spots,
    MotorcycleSpot::TYPE => $num_motorcycle_spots,
]);

echo "How many spots does it have?" . PHP_EOL;

echo $lot->getNumberOfSpots() . PHP_EOL;

echo "How many spots are unoccupied?" . PHP_EOL;

echo $lot->getNumberOfSpotsRemaining() . PHP_EOL;

echo "Is the parking lot full?" . PHP_EOL;

echo $lot->isFull() ? 'Yes, it is full.' : 'No, ' . $lot->getNumberOfSpotsRemaining() . ' spots remain.' . PHP_EOL;

echo "Parking {$num_cars} cars:" . PHP_EOL;
for ($i = 0 ; $i < $num_cars ; $i++) {
    $car = new Car($i);
    try {
        $lot->parkUser($car);
    }
    catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
        echo "Parked {$i} cars.";
        exit(1);
    }
}

echo "How many spots are unoccupied?" . PHP_EOL;

echo $lot->getNumberOfSpotsRemaining() . PHP_EOL;

?>
