<center>

[comment]: <img src="./media/media/image1.png" style="width:1.088in;height:1.46256in" alt="escudo.png" />

![./media/media/image1.png](./media/logo-upt.png)

**UNIVERSIDAD PRIVADA DE TACNA**

**FACULTAD DE INGENIERIA**

**Escuela Profesional de Ingeniería de Sistemas**

**Proyecto *FANPAGE***

Curso: *Calidad y pruebas de software*

Docente: *Mg. Patrick Cuadros Quiroga*

Integrantes:

Chambilla Zuñiga, Josue Abraham E.		(2020067575)

Llantay Machaca, Marjorie Garce 		    (2020068951)

  **Tacna – Perú**
                        
  ***2024***



</center>

Sistema *FanPage*


Documento Informe de Calidad

Versión *1.0*



|CONTROL DE VERSIONES||||||
| :-: | :- | :- | :- | :- | :- |
|Versión|Hecha por|Revisada por|Aprobada por|Fecha|Motivo|
|1\.0|JACZ - MGLLM|JACZ - MGLLM|JACZ - MGLLM|27/09/2024|Versión Original|



<div style="page-break-after: always; visibility: hidden">\pagebreak</div>

## ÍNDICE
1. [Antecedentes o introducción](#antecedentes-o-introducción)
2. [Titulo](#titulo)
3. [Autores](#autores)
4. [Planteamiento del problema](#planteamiento-del-problema)
    - 4.1 [Problema](#problema)
    - 4.2 [Justificación](#justificación)
    - 4.3 [Alcance](#alcance)
5. [Objetivos](#objetivos)
    - 5.1 [General](#general)
    - 5.2 [Específicos](#específicos)
6. [Referentes teóricos](#referentes-teóricos)
7. [Desarrollo de la propuesta](#desarrollo-de-la-propuesta)
    - 7.1 [Tecnología de información](#tecnología-de-información)
    - 7.2 [Metodología técnicas usadas](#metodología-técnicas-usadas)
8. [Cronograma](#cronograma)
9. [Conclusión](#conclusión)

## Resumen

El proyecto FANPAGE tiene como objetivo desarrollar una plataforma interactiva diseñada para mejorar la conexión entre los seguidores y su comunidad favorita. A través de esta página, los usuarios podrán acceder a contenido exclusivo, interactuar con publicaciones, compartir opiniones y participar en eventos en tiempo real. La plataforma está diseñada para brindar una experiencia de usuario atractiva y fácil de usar, garantizando que los fanáticos puedan mantenerse al día con las novedades y actividades de su interés.

La arquitectura del sistema está diseñada para ser modular y escalable, permitiendo la integración de diversas funcionalidades Además, se ha incorporado un sistema de gestión de usuarios, que asegura la seguridad y privacidad de los datos, a la vez que permite una experiencia personalizada para cada fanático.

---

## Abstract

The FANPAGE project aims to develop an interactive platform designed to improve the connection between followers and their favorite community. Through this page, users will be able to access exclusive content, interact with publications, share opinions and participate in events in real time. The platform is designed to provide an engaging and easy-to-use user experience, ensuring that fans can stay up to date with news and activities of interest to them.

The system architecture is designed to be modular and scalable, allowing the integration of various functionalities. In addition, a user management system has been incorporated, which ensures the security and privacy of data, while allowing a personalized experience for each user. fanatic.

---

## 1. Antecedentes o introducción

1.	El comercio electrónico permite a los usuarios comprar productos desde cualquier lugar. FANPAGE es una plataforma web que conecta a los seguidores con productos exclusivos, mejorando su experiencia de compra con una interfaz sencilla y un sistema de pagos seguro.

2.	Las marcas necesitan plataformas donde puedan interactuar y vender a sus seguidores. FANPAGE facilita la compra de productos y ofrece contenido personalizado.

3.	Hoy en día, los usuarios buscan experiencias de compra personalizadas. FANPAGE combina la venta de productos exclusivos con una interfaz accesible, permitiendo a las marcas gestionar ventas de manera eficiente y segura.

---

## 2. Titulo
FANPAGE

---

## 3. Autores
- Chambilla Zuñiga Josue Abraham E.
- Llantay Machaca Marjorie Garce

---

## 4. Planteamiento del problema

### 4.1 Problema
En la actualidad, muchas marcas y comunidades carecen de plataformas especializadas donde puedan vender productos exclusivos a sus seguidores de manera eficiente. Las plataformas de comercio electrónico generalistas no siempre permiten una personalización adecuada o una interacción directa con la comunidad. Esto dificulta la gestión de inventarios, la promoción de productos exclusivos y la fidelización de clientes.

### 4.2 Justificación
FANPAGE busca cubrir esta necesidad creando una plataforma web personalizada para la venta de productos específicos, permitiendo a las marcas ofrecer una experiencia única a sus seguidores. La plataforma proporcionará herramientas para procesar pagos de forma segura y crear un vínculo directo con la comunidad, algo que no se logra fácilmente con plataformas de comercio electrónico genéricas.

### 4.3 Alcance
El proyecto FANPAGE abarcará la creación de una plataforma web que permita a los usuarios registrar sus cuentas, explorar productos, añadir artículos a su carrito de compras y realizar pagos de forma segura. Desde el lado administrativo, se incluirán funcionalidades para gestionar productos y órdenes de compra. El sistema también ofrecerá integración con diferentes métodos de pago y garantizará la protección de los datos de los usuarios.

---

## 5. Objetivos

### 5.1 General
Desarrollar una plataforma web personalizada que permita a los usuarios adquirir productos exclusivos de una marca o comunidad, facilitando la gestión, los pagos y la interacción directa con los seguidores.

### 5.2 Específicos
- Crear un sistema de gestión de usuarios que permita registrarse, iniciar sesión y personalizar la experiencia de compra.
- Desarrollar un panel administrativo para la gestión de inventarios, productos y seguimiento de órdenes.
- Integrar métodos de pago seguros y confiables que faciliten las transacciones dentro de la plataforma.
- Garantizar la protección de datos de los usuarios mediante la implementación de buenas prácticas de seguridad.

---

## 6. Referentes teóricos
- ** Diagrama de Componentes
 ![image](https://github.com/user-attachments/assets/9894244b-de11-4871-8943-473612456ade)
---
- ** Diagrama de Arquiitectura
 ![image](https://github.com/user-attachments/assets/9eb51977-a735-4f4f-8b32-053904888ee9)
---
- ** Diagrama de Clases
  ![image](https://github.com/user-attachments/assets/c0b49301-e0c0-4b02-a517-288ea72a1299)
  ---
- ** Diagrama de Casos de Uso
  ![image](https://github.com/user-attachments/assets/12ad985f-52b7-4e31-be6a-c35ffbd4b968)
  ---
- ** Diagrama de Casos de Uso
  ![image](https://github.com/user-attachments/assets/4cfcad7c-9c60-4427-928f-304f60b0904f)
  
---

## 7. Desarrollo de la propuesta

### 7.1 Tecnología de información 
- **GitHub Actions**: Automatiza la construcción y despliegue de tu aplicación. Cada vez que haces un push al repositorio,    GitHub Actions construye la imagen de Docker y la sube a Docker Hub.
- **Docker Hub**: Almacena y distribuye la imagen de tu aplicación. Las imágenes construidas en GitHub Actions se suben a   
  Docker Hub para su fácil despliegue en diferentes entornos.
- **Base de datos en la nube**: Utiliza un servicio en la nube (como AWS RDS o Google Cloud SQL) para alojar la base de 
  datos de la aplicación. La aplicación desplegada se conecta a la base de datos mediante un host configurado, garantizando 
  que los datos estén accesibles y seguros en la nube.

### 7.2 Metodología, técnicas usadas

![image](https://github.com/user-attachments/assets/6c69f55b-49ec-42a6-920a-eba18343e6ec)
![image](https://github.com/user-attachments/assets/d0c6578e-a07a-4e0b-af3e-5018e28dd728)

  4031 reglas aplicadas a 29 archivos, con un archivo de bloqueo identificado como src/composer.lock.

---

## 9. Cronograma

![image](https://github.com/user-attachments/assets/5367ee65-bb2a-4011-bc20-2749b3155b5f)

---

## 9. Conclusión 
- **Automatización eficiente**: La integración de GitHub Actions con Docker Hub permite automatizar la construcción y despliegue continuo de aplicaciones, asegurando un flujo de trabajo ágil y eficiente en el desarrollo y distribución de contenedores.
- **Escalabilidad y gestión centralizada**: El uso de una base de datos en la nube proporciona alta disponibilidad, escalabilidad y seguridad, permitiendo que la aplicación desplegada mantenga un acceso confiable a los datos desde cualquier host o servidor.

