# Vehicle Service Management System — Docker + Kubernetes

## Pages added
- Customer home and booking: `http://localhost:30080`
- Customer registration: `http://localhost:30080/register.php`
- Customer login: `http://localhost:30080/login.php`
- Customer appointment history: `http://localhost:30080/my-appointments.php`
- Admin login: `http://localhost:30080/admin-login.php`
- Admin dashboard: `http://localhost:30080/admin-dashboard.php`
- Database administration (phpMyAdmin): `http://localhost:30081`

Admin demo login: username `admin`; password `VehicleAdmin@2026`.
phpMyAdmin login with the MySQL root account: username `root`; password `demo_root_change_me`.

These are classroom demo credentials only. Change them before any public deployment. Do not expose phpMyAdmin publicly.

## Apply changes to an existing local Kubernetes project

From this folder:
```powershell
docker build -t vehicle-service-app:1.0 .
kubectl apply -f k8s/web.yaml
kubectl apply -f k8s/phpmyadmin.yaml
kubectl rollout restart deployment/vehicle-service
kubectl rollout status deployment/vehicle-service
kubectl rollout status deployment/vehicle-service-phpmyadmin
kubectl get pods
kubectl get services
```
Open the URLs above after the pods are ready. The phpMyAdmin service uses NodePort 30081.

If your existing Kubernetes resources were created with this project’s previous files, applying the updated manifests updates the web deployment environment and adds phpMyAdmin. Existing MySQL data is preserved; `db_connect.php` creates the `users` table automatically if it does not exist.

## Docker Compose alternative
```powershell
docker compose up --build -d
```
- Website: `http://localhost:8080`
- phpMyAdmin: `http://localhost:8081`

Stop:
```powershell
docker compose down
```

## Kubernetes deployment and scaling
```powershell
kubectl get nodes
kubectl get pods
kubectl get deployments
kubectl get services
kubectl scale deployment vehicle-service --replicas=2
kubectl get pods
```

## Technology
PHP 8.2 + Apache, MySQL 8.4, Docker, Docker Compose, Kubernetes, kubectl, phpMyAdmin.

## Security note
This is a local coursework demonstration. Admin credentials are demo credentials; customer registration uses password hashing. Admin access should use stronger secrets and production security controls before any real deployment.
