<?php



trait DatabaseTrait
{
    public static function getConnection()
    {
        //Create connection
        $conn = mysqli_connect('127.0.0.1', 'ryskit', 'marcatrysk', 'groepsvervoer');
        //Check connection
        if (mysqli_connect_errno()) {
            //Connection failed
            return 'Failed to connect to MySQL' . mysqli_connect_error();
        }
        return $conn;
    }
}
