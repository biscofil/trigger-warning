# Trigger Warning

## Docker configuration change (Dockerfile)

```shell

# helm uninstall trigger-warning  --namespace=trigger-warning

kubectl create namespace trigger-warning
kubectl create secret generic trigger-warning-env --from-env-file=.env --namespace=trigger-warning

# docker run -p 8080:80 biscofil/trigger_warning:latest
# visit localhost:8080

# Metrics
kubectl apply -f https://github.com/kubernetes-sigs/metrics-server/releases/download/v0.5.0/components.yaml
kubectl patch deployment metrics-server -n kube-system --patch "$(cat metric-server-patch.yaml)"

helm repo add prometheus-community https://prometheus-community.github.io/helm-charts
helm repo add stable https://charts.helm.sh/stable
helm repo update
kubectl create namespace monitoring
helm install kind-prometheus prometheus-community/kube-prometheus-stack \
  --namespace monitoring \
  --set prometheus.service.nodePort=30000 \
  --set prometheus.service.type=NodePort \
  --set grafana.service.nodePort=31000 \
  --set grafana.service.type=NodePort \
  --set alertmanager.service.nodePort=32000 \
  --set alertmanager.service.type=NodePort \
  --set prometheus-node-exporter.service.nodePort=32001 \
  --set prometheus-node-exporter.service.type=NodePort
# kubectl apply -f go-app-podmonitor.yaml


Download metrics-server:
wget https://github.com/kubernetes-sigs/metrics-server/releases/download/v0.5.0/components.yaml
Remove (legacy) metrics server:
kubectl delete -f components.yaml  
Edit downloaded file and add - --kubelet-insecure-tls to args list:
...
labels:
    k8s-app: metrics-server
spec:
  containers:
  - args:
    - --cert-dir=/tmp
    - --secure-port=443
    - --kubelet-preferred-address-types=InternalIP,ExternalIP,Hostname
    - --kubelet-use-node-status-port
    - --metric-resolution=15s
    - --kubelet-insecure-tls # add this line
...
#Create service once again:
kubectl apply -f components.yaml



docker build -t biscofil/trigger_warning:latest .
kind load docker-image biscofil/trigger_warning:latest
helm package helm
helm upgrade --install trigger-warning ./helm --namespace=trigger-warning

kubectl port-forward svc/app 8080:80 --namespace=trigger-warning &

# Shell into the pod
# php artisan approve_user

docker build -t biscofil/trigger_warning:latest .
docker push biscofil/trigger_warning:latest
docker run -it biscofil/trigger_warning:latest /bin/bash
# php artisan test



```
