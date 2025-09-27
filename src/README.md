# Trigger Warning

[![pipeline status](https://gitlab.com/biscofil/trigger_warning/badges/master/pipeline.svg)](https://gitlab.com/biscofil/trigger_warning/-/commits/master)

[![coverage report](https://gitlab.com/biscofil/trigger_warning/badges/master/coverage.svg)](https://gitlab.com/biscofil/trigger_warning/-/commits/master)

## Docker configuration change (Dockerfile)

```shell

kubectl create namespace trigger-warning
kubectl create secret generic trigger-warning-env --from-env-file=.env --namespace=trigger-warning

# docker run -p 8080:80 biscofil/trigger_warning:latest
# visit localhost:8080

docker build -t biscofil/trigger_warning:latest .
kind load docker-image biscofil/trigger_warning:latest
helm package helm
helm upgrade --install trigger-warning ./helm --namespace=trigger-warning

# Shell into the pod
# php artisan approve_user

docker build -t registry.gitlab.com/biscofil/trigger_warning .
docker push registry.gitlab.com/biscofil/trigger_warning
docker run -it registry.gitlab.com/biscofil/trigger_warning /bin/bash
# php artisan test



```
