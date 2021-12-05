Маифесты лежат в папке kubernetes

### Запустить можно так: ###
```shell
cd helm && helm dependency update && helm install otus-hw .
```

Проблема с запуском контейнеров решается так:
```shell
kubectl create role pod-reader --verb=get --verb=list --verb=watch --resource=pods,services,deployments,jobs

kubectl create rolebinding default-pod-reader --role=pod-reader --serviceaccount=default:default --namespace=default
```

Запуск яндекс танка:
* [для minikube] `sudo kubectl port-forward service/otus-hw-nginx-controller 80:80 --address='0.0.0.0'`
* Положить свой токен в файл `./tank/token.txt`
* Запустить тест командой  `docker run --rm -v $(pwd)/tank/:/var/loadtest --net=host -it direvius/yandex-tank`