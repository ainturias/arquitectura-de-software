package Database;


import java.sql.Connection;
import java.sql.DriverManager;
import javax.swing.JOptionPane;


/**
 *
 * @author Alvaro
 */
public class Conexion {
    Connection connection;
    
    public Connection getConnection(){
        String url = "jdbc:mysql://localhost:3306/trescapas";
        String user = "root";
        String password = "";
        try {
            Class.forName("com.mysql.jdbc.Driver");
            connection = DriverManager.getConnection(url, user, password);
            System.out.println(connection);
        } catch (Exception e) {
            JOptionPane.showMessageDialog(null, "Error al conectar db "+e,"Error",JOptionPane.ERROR_MESSAGE);
        }
        return connection;
    }
    public static void main(String[] args) {
       
        Conexion conn = new Conexion();
         System.out.println(conn.getConnection());
        
    }
}
