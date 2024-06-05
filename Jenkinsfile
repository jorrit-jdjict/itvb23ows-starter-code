pipeline {
    agent any
    
    stages {
        stage('Checkout') {
            steps {
                // Haal de nieuwste code op van de Git-repository (main branch)
                checkout scm
            }
        }

        stage('Test') {
            steps {
                dir("hive") {
                    sh 'composer update'
                    sh 'php vendor/bin/phpunit tests'
                }
            }
        }
        
        stage('SonarQube Analysis') {
            environment {
                scannerHome = tool 'sonarscanner'; // Controleer of 'sonarscanner' in Jenkins is geconfigureerd
                projectName = 'itvb23ows-starter-code'
            }
            steps {
                // Voer de SonarQube-scanner uit
                script {
                    withSonarQubeEnv() {
                        sh '''
                            $scannerHome/bin/sonar-scanner \
                            -Dsonar.projectKey=$projectName
                        '''
                    }
                }
            }
        }
    }
}
